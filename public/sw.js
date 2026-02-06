// Service Worker for Web Push Notifications
// Place this file in: public/sw.js

self.addEventListener('push', function(event) {
    if (event.data) {
        const data = event.data.json();

        const options = {
            body: data.body || '',
            icon: data.icon || '/images/notification-icon.png',
            badge: data.badge || '/images/notification-badge.png',
            vibrate: data.vibrate || [200, 100, 200],
            data: data.data || {},
            actions: data.actions || [],
            tag: data.tag || 'notification-' + Date.now(),
            requireInteraction: data.requireInteraction || false,
        };

        event.waitUntil(
            self.registration.showNotification(data.title || 'Notification', options)
        );
    }
});

// Handle notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const urlToOpen = event.notification.data.url || '/';

    event.waitUntil(
        clients.matchAll({
            type: 'window',
            includeUncontrolled: true
        })
        .then(function(clientList) {
            // If a window is already open, focus it
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === urlToOpen && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise, open a new window
            if (clients.openWindow) {
                return clients.openWindow(urlToOpen);
            }
        })
    );
});

// Handle notification close
self.addEventListener('notificationclose', function(event) {
    console.log('Notification closed', event.notification.tag);
});
