// Shared service worker for the Teacher and Principal portals.
// Registered separately by each portal's layout with its own scope
// (/teacher or /principal), so this one file serves both.
//
// Strategy: network-first for page navigations (so users always get fresh
// data when online, falling back to the last-cached version of that same
// page when offline); cache-first for static assets (css/js/images/fonts)
// for fast repeat loads. POST/PUT/DELETE requests are never intercepted —
// offline queuing for form submissions is handled entirely by
// offline-queue.js at the page level, not here.

const CACHE_NAME = 'portal-cache-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key !== CACHE_NAME)
                    .map((key) => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Never intercept non-GET requests — form submissions go straight to
    // the network (or are queued client-side by offline-queue.js).
    if (request.method !== 'GET') {
        return;
    }

    // Skip cross-origin requests (CDN scripts, fonts, etc.) — let the
    // browser handle those normally.
    if (new URL(request.url).origin !== self.location.origin) {
        return;
    }

    const isNavigation = request.mode === 'navigate';

    if (isNavigation) {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match('/offline.html')))
        );
        return;
    }

    // Static assets — cache-first, refresh in the background.
    event.respondWith(
        caches.match(request).then((cached) => {
            const fetchPromise = fetch(request)
                .then((response) => {
                    if (response && response.ok) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, clone));
                    }
                    return response;
                })
                .catch(() => cached);

            return cached || fetchPromise;
        })
    );
});
