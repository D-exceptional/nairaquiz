// Cache versioning
const CACHE_NAME = 'v1';
const CACHE_EXPIRY_TIME = 3600000; // 1 hour in milliseconds
const CACHE_TIMESTAMP_KEY = 'cacheTimestamp';

// Files to cache
const ASSETS_TO_CACHE = [
  "/",
  "/assets/js/sweetalert2.all.min.js",
  "/assets/js/main.js",
  "/assets/scripts/login.js",
];

// Install event: Cache files
self.addEventListener('install', (event) => {
  self.skipWaiting(); // Activate worker immediately
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[Service Worker] Caching assets');
      return cache.addAll(ASSETS_TO_CACHE).catch((error) => {
        console.error('[Service Worker] Failed to cache assets:', error);
      });
    })
  );
});

// Activate event: Clean up old caches and check cache expiry
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      // Get the current time and compare with the stored cache timestamp
      const now = Date.now();
      return caches.open(CACHE_NAME).then((cache) => {
        // Get the cache timestamp from the cache storage or set it to the current time if not set
        return cache.match(CACHE_TIMESTAMP_KEY).then((timestampResponse) => {
          if (timestampResponse) {
            return timestampResponse.json().then((timestampData) => {
              const cacheTimestamp = timestampData.timestamp;
              if (now - cacheTimestamp > CACHE_EXPIRY_TIME) {
                // If cache has expired (more than 1 hour), update the cache
                console.log('[Service Worker] Cache expired, updating...');
                return updateCache(cache);
              } else {
                console.log('[Service Worker] Cache is still valid.');
              }
            });
          } else {
            // If no timestamp exists, this is the first installation, so cache and set timestamp
            console.log('[Service Worker] First install, caching assets...');
            return updateCache(cache);
          }
        });
      });
    })
  );
});

// Update the cache by fetching the latest resources from the network
function updateCache(cache) {
  return cache.addAll(ASSETS_TO_CACHE).then(() => {
    // Update the cache timestamp to the current time
    cache.put(CACHE_TIMESTAMP_KEY, new Response(JSON.stringify({ timestamp: Date.now() })));
    console.log('[Service Worker] Cache updated successfully.');
  }).catch((error) => {
    console.error('[Service Worker] Failed to update cache:', error);
  });
}

// Fetch event: Network first for CSS and JS, cache first for others
self.addEventListener('fetch', (event) => {
  const requestUrl = new URL(event.request.url);

  // Determine cache strategy based on request URL
  if (
    requestUrl.pathname.startsWith("/assets/css/") ||
    requestUrl.pathname.startsWith("/assets/js/") ||
    requestUrl.pathname.startsWith("/assets/scripts/") ||
    requestUrl.pathname.startsWith("/dashboard/scripts/") ||
    requestUrl.pathname.startsWith("/admin/scripts/") ||
    requestUrl.pathname.startsWith("/admin/pages/scripts/")
  ) {
    // Network first strategy
    event.respondWith(
      fetch(event.request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.ok) {
            const clonedResponse = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => {
              cache.put(event.request, clonedResponse).catch((error) => {
                console.error(
                  "[Service Worker] Failed to cache network response:",
                  error
                );
              });
            });
            return networkResponse;
          }
          throw new Error("Network response was not ok");
        })
        .catch(() => {
          return caches.match(event.request).then((cachedResponse) => {
            if (cachedResponse) {
              return cachedResponse;
            }
            return new Response("Offline", {
              status: 503,
              statusText: "Service Unavailable",
            });
          });
        })
    );
  } else {
    // Cache first strategy for other assets
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          console.log(
            `[Service Worker] Returning cached response for ${event.request.url}`
          );
          return cachedResponse;
        }
        return fetch(event.request).catch((error) => {
          console.error("[Service Worker] Fetch failed:", error);
          return new Response("Offline", {
            status: 503,
            statusText: "Service Unavailable",
          });
        });
      })
    );
  }
});
