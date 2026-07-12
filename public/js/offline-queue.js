// Offline data-capture queue for the Principal Portal.
// Scope: School Basic Info (phone/email only, no photo), Student Statistics,
// and Physical Resources (excluding the Budget tab and any file inputs —
// those stay online-only, see physical-resources.blade.php).
//
// Forms opt in via `data-offline-section="basic_info|student_stats|physical_resources"`.
// While online, forms submit normally (untouched). While offline, submission
// is intercepted, non-file fields are stored in IndexedDB, and the browser's
// native navigation is prevented. Queued items are replayed automatically
// when connectivity returns.

(function () {
    'use strict';

    const DB_NAME = 'principal_offline_queue';
    const DB_VERSION = 1;
    const STORE = 'pending';
    const SYNC_URL = '/principal/school';

    function openDb() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);
            req.onupgradeneeded = () => {
                const db = req.result;
                if (!db.objectStoreNames.contains(STORE)) {
                    db.createObjectStore(STORE, { keyPath: 'id', autoIncrement: true });
                }
            };
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    function withStore(mode, callback) {
        return openDb().then((db) => new Promise((resolve, reject) => {
            const tx = db.transaction(STORE, mode);
            const store = tx.objectStore(STORE);
            const result = callback(store);
            tx.oncomplete = () => resolve(result);
            tx.onerror = () => reject(tx.error);
        }));
    }

    function formToFields(form) {
        const fields = [];
        const formData = new FormData(form);
        for (const [name, value] of formData.entries()) {
            if (value instanceof File) continue; // no file uploads offline
            fields.push([name, value]);
        }
        return fields;
    }

    function addToQueue(section, form) {
        const item = {
            section,
            fields: formToFields(form),
            label: form.getAttribute('data-offline-label') || section,
            timestamp: Date.now(),
            status: 'pending',
            error: null,
        };
        return withStore('readwrite', (store) => store.add(item)).then(() => {
            dispatchUpdate();
        });
    }

    function getAll() {
        return openDb().then((db) => new Promise((resolve, reject) => {
            const tx = db.transaction(STORE, 'readonly');
            const req = tx.objectStore(STORE).getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        }));
    }

    function removeItem(id) {
        return withStore('readwrite', (store) => store.delete(id)).then(dispatchUpdate);
    }

    function updateItem(id, changes) {
        return openDb().then((db) => new Promise((resolve, reject) => {
            const tx = db.transaction(STORE, 'readwrite');
            const store = tx.objectStore(STORE);
            const getReq = store.get(id);
            getReq.onsuccess = () => {
                const item = getReq.result;
                if (!item) return resolve();
                Object.assign(item, changes);
                store.put(item);
            };
            tx.oncomplete = () => { resolve(); dispatchUpdate(); };
            tx.onerror = () => reject(tx.error);
        }));
    }

    function dispatchUpdate() {
        getAll().then((items) => {
            document.dispatchEvent(new CustomEvent('principal-offline-queue-updated', { detail: { items } }));
        });
    }

    function csrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    async function refreshCsrfToken() {
        try {
            const res = await fetch(window.location.pathname, { credentials: 'same-origin' });
            const html = await res.text();
            const match = html.match(/<meta name="csrf-token" content="([^"]+)">/);
            if (match) {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta) meta.setAttribute('content', match[1]);
                return match[1];
            }
        } catch (e) { /* ignore, will just fail the retry below */ }
        return csrfToken();
    }

    async function syncOne(item, tokenOverride) {
        const body = new URLSearchParams();
        item.fields.forEach(([name, value]) => body.append(name, value));

        const res = await fetch(SYNC_URL, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': tokenOverride || csrfToken(),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body,
        });

        if (res.status === 419 && !tokenOverride) {
            const fresh = await refreshCsrfToken();
            return syncOne(item, fresh);
        }

        let json = null;
        try { json = await res.json(); } catch (e) { /* non-JSON response */ }

        if (res.ok && json && json.success) {
            return { ok: true };
        }

        const message = (json && json.message) || `Sync failed (HTTP ${res.status})`;
        return { ok: false, message };
    }

    let flushing = false;

    async function flush() {
        if (flushing || !navigator.onLine) return;
        flushing = true;
        try {
            const items = await getAll();
            for (const item of items.filter((i) => i.status !== 'failed')) {
                try {
                    const result = await syncOne(item);
                    if (result.ok) {
                        await removeItem(item.id);
                    } else {
                        await updateItem(item.id, { status: 'failed', error: result.message });
                    }
                } catch (e) {
                    // Network error mid-sync (connection dropped again) — leave
                    // as pending, will retry on the next online event.
                    break;
                }
            }
        } finally {
            flushing = false;
        }
    }

    function retryItem(id) {
        return updateItem(id, { status: 'pending', error: null }).then(flush);
    }

    window.PrincipalOfflineQueue = {
        add: addToQueue,
        getAll,
        remove: removeItem,
        retry: retryItem,
        flush,
    };

    // Reactive online/offline state for Alpine components — used to disable
    // the Budget tab and photo-upload fields while offline (those two stay
    // online-only; everything else in Physical Resources / Student Stats /
    // Basic Info works offline via the queue above).
    document.addEventListener('alpine:init', () => {
        Alpine.data('connectivity', () => ({
            online: navigator.onLine,
            init() {
                window.addEventListener('online', () => { this.online = true; });
                window.addEventListener('offline', () => { this.online = false; });
            },
        }));
    });

    // Intercept opted-in forms while offline.
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.hasAttribute('data-offline-section')) return;
        if (navigator.onLine) return; // let it submit normally

        e.preventDefault();
        const section = form.getAttribute('data-offline-section');
        addToQueue(section, form).then(() => {
            document.dispatchEvent(new CustomEvent('principal-offline-saved', { detail: { section } }));
        });
    }, true);

    window.addEventListener('online', flush);
    document.addEventListener('DOMContentLoaded', () => {
        dispatchUpdate();
        if (navigator.onLine) flush();
    });
})();
