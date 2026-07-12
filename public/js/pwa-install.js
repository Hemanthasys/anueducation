// Shared PWA install/registration logic for the Teacher and Principal portals.
// Expects the page to optionally contain:
//   - #pwa-install-btn      — shown/enabled only when a native install prompt is available (Chrome/Edge/Android)
//   - #pwa-ios-banner       — shown on iOS Safari (no native prompt exists there)
//   - #pwa-ios-banner-dismiss — dismiss button inside the iOS banner

(function () {
    'use strict';

    const path = window.location.pathname;
    const scope = path.startsWith('/principal') ? '/principal'
        : path.startsWith('/teacher') ? '/teacher'
        : null;

    if (!scope) return;

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope }).catch(() => { /* non-fatal */ });
        });
    }

    let deferredPrompt = null;
    const installBtn = document.getElementById('pwa-install-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        if (installBtn) installBtn.style.display = 'inline-flex';
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            await deferredPrompt.userChoice;
            deferredPrompt = null;
            installBtn.style.display = 'none';
        });
    }

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        if (installBtn) installBtn.style.display = 'none';
    });

    // iOS Safari has no beforeinstallprompt — show a manual instruction banner.
    const isIos = /iphone|ipad|ipod/i.test(window.navigator.userAgent) && !window.MSStream;
    const isStandalone = ('standalone' in window.navigator) && window.navigator.standalone;
    const iosBanner = document.getElementById('pwa-ios-banner');
    const dismissedKey = 'pwa_ios_banner_dismissed_' + scope;

    if (isIos && !isStandalone && iosBanner && !localStorage.getItem(dismissedKey)) {
        iosBanner.style.display = 'flex';
        const dismissBtn = document.getElementById('pwa-ios-banner-dismiss');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                iosBanner.style.display = 'none';
                localStorage.setItem(dismissedKey, '1');
            });
        }
    }
})();
