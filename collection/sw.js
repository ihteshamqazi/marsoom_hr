/* ====== Collection PWA - Service Worker ====== */
const VERSION = 'v1.0.0';
const PRECACHE = `collection-precache-${VERSION}`;
const RUNTIME  = `collection-runtime-${VERSION}`;

const PRECACHE_ASSETS = [
  '/collection/',
  '/collection/offline.html',
  // Add your core CSS/JS here (adjust paths)
  // '/collection/assets/css/app.css',
  // '/collection/assets/js/app.js',
];

self.addEventListener('install', (event) => {
  event.waitUntil(caches.open(PRECACHE).then(cache => cache.addAll(PRECACHE_ASSETS)));
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then(keys => Promise.all(keys
      .filter(k => ![PRECACHE, RUNTIME].includes(k))
      .map(k => caches.delete(k))
    ))
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  const req = event.request;
  const url = new URL(req.url);
  if (req.method !== 'GET') return;

  if (url.origin === 'https://fonts.googleapis.com' || url.origin === 'https://fonts.gstatic.com') {
    event.respondWith(fontsStrategy(req));
    return;
  }

  if (req.mode === 'navigate' || (req.headers.get('accept') || '').includes('text/html')) {
    event.respondWith(
      fetch(req).then(res => {
        const copy = res.clone();
        caches.open(RUNTIME).then(c => c.put(req, copy));
        return res;
      }).catch(async () => {
        const cached = await caches.match(req);
        return cached || caches.match('/collection/offline.html');
      })
    );
    return;
  }

  if (url.origin === self.location.origin) {
    event.respondWith(staleWhileRevalidate(req));
    return;
  }

  event.respondWith(fetch(req).catch(() => caches.match(req)));
});

async function staleWhileRevalidate(request) {
  const cache = await caches.open(RUNTIME);
  const cached = await cache.match(request);
  const fetchPromise = fetch(request).then((networkResponse) => {
    if (networkResponse && networkResponse.status === 200) {
      cache.put(request, networkResponse.clone());
    }
    return networkResponse;
  }).catch(() => cached);
  return cached || fetchPromise;
}

async function fontsStrategy(request) {
  const cache = await caches.open(`${RUNTIME}-fonts`);
  const cached = await cache.match(request);
  const fetchPromise = fetch(request).then(res => {
    if (res && res.status === 200) cache.put(request, res.clone());
    return res;
  }).catch(() => cached);
  return cached || fetchPromise;
}
