// sw.js - Improved Service Worker
const CACHE_NAME = 'marsoom-hr-v2';

// Cache essential files
const urlsToCache = [
  './',
  './index.php/users2/mobile_dashboard',
  './index.php/users2/pwa_launch',
  './assets/logo1.png',
  './style.css'
];

self.addEventListener('install', function(event) {
  console.log('Service Worker installing...');
  
  self.skipWaiting();
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('Cache opened, adding URLs...');
        return cache.addAll(urlsToCache).catch(function(error) {
          console.log('Cache addAll failed, trying individual files...', error);
          // Fallback: cache files individually
          return Promise.all(
            urlsToCache.map(function(url) {
              return cache.add(url).catch(function(e) {
                console.log('Failed to cache:', url, e);
                return Promise.resolve();
              });
            })
          );
        });
      })
      .then(function() {
        console.log('All URLs processed');
      })
  );
});

self.addEventListener('activate', function(event) {
  console.log('Service Worker activating...');
  
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            console.log('Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
  
  self.clients.claim();
});

self.addEventListener('fetch', function(event) {
  if (event.request.method !== 'GET') return;
  
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        if (response) {
          return response;
        }
        
        return fetch(event.request)
          .then(function(networkResponse) {
            if (networkResponse && networkResponse.status === 200) {
              const responseToCache = networkResponse.clone();
              caches.open(CACHE_NAME)
                .then(function(cache) {
                  cache.put(event.request, responseToCache);
                });
            }
            return networkResponse;
          })
          .catch(function(error) {
            console.log('Fetch failed:', error);
            // Return offline page or fallback
            return caches.match('./');
          });
      })
  );
});