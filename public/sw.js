/**
 * Livingstone Diocese Catholic Youth Ministry PWA Service Worker
 * Architecture: Stale-While-Revalidate for Static Assets, Network-First for HTML/Data, Safe Exclusion for Livewire/Mutations
 */

const CACHE_VERSION = 'ldcym-pwa-v1.0.0';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;

const PRECACHE_ASSETS = [
    '/offline',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
    '/icons/icon-maskable-192x192.png',
    '/icons/icon-maskable-512x512.png',
    '/icons/icon.svg',
    '/icons/apple-touch-icon.png'
];

// Install Event - Precache essential assets and offline fallback
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - Clean up stale cache versions
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== STATIC_CACHE && key !== DYNAMIC_CACHE) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event - Context-aware caching strategies
self.addEventListener('fetch', (event) => {
    const request = event.request;
    const url = new URL(request.url);

    // 1. Never intercept non-GET requests (CSRF forms, POST login, Livewire mutations)
    if (request.method !== 'GET') {
        return;
    }

    // 2. Bypass Service Worker for sensitive authenticated mutation routes & Filament Admin actions
    if (
        url.pathname.startsWith('/livewire/') ||
        url.pathname.startsWith('/admin/') ||
        url.pathname.includes('logout') ||
        url.pathname.includes('/sanctum/')
    ) {
        return;
    }

    // 3. Static Assets (Images, Icons, Webfonts, Styles, Scripts): Stale-While-Revalidate
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'font' ||
        request.destination === 'image' ||
        url.pathname.startsWith('/icons/') ||
        url.hostname.includes('fonts.googleapis.com') ||
        url.hostname.includes('fonts.gstatic.com') ||
        url.hostname.includes('cdn.tailwindcss.com')
    ) {
        event.respondWith(
            caches.open(STATIC_CACHE).then((cache) => {
                return cache.match(request).then((cachedResponse) => {
                    const fetchPromise = fetch(request).then((networkResponse) => {
                        if (networkResponse && networkResponse.status === 200) {
                            cache.put(request, networkResponse.clone());
                        }
                        return networkResponse;
                    }).catch(() => cachedResponse);

                    return cachedResponse || fetchPromise;
                });
            })
        );
        return;
    }

    // 4. HTML Navigation Pages: Network-First with Offline Fallback
    if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(DYNAMIC_CACHE).then((cache) => {
                            cache.put(request, copy);
                        });
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // Return cached offline page
                    const offlinePage = await caches.match('/offline');
                    return offlinePage || new Response('Offline: Connection unavailable.', {
                        status: 503,
                        headers: { 'Content-Type': 'text/plain' }
                    });
                })
        );
        return;
    }

    // Default: Network with Dynamic Cache fallback
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
