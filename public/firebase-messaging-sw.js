// Firebase Cloud Messaging Service Worker
// File: public/firebase-messaging-sw.js

// Import Firebase scripts (CDN)
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js');

// Firebase configuration - Replace with your config
const firebaseConfig = {

  apiKey: "AIzaSyCYCmxKcnO6hH5ZQU6NghLS5R2TA_eGKSw",

  authDomain: "kandura-34abc.firebaseapp.com",

  projectId: "kandura-34abc",

  storageBucket: "kandura-34abc.firebasestorage.app",

  messagingSenderId: "928328141726",

  appId: "1:928328141726:web:b3bfb75f81a5eb6050648f",

  measurementId: "G-PME0BC223E"

};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/images/notification-icon.png',
        badge: '/images/notification-badge.png',
        data: payload.data || {},
        tag: payload.data?.type || 'notification',
        requireInteraction: false,
        vibrate: [200, 100, 200]
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[firebase-messaging-sw.js] Notification click received.');

    event.notification.close();

    const data = event.notification.data;
    let urlToOpen = '/';

    // Navigate based on notification type
    if (data.action) {
        switch(data.action) {
            case 'go_to_order_list':
                urlToOpen = '/orders';
                break;
            case 'go_to_order_details':
                urlToOpen = `/orders/${data.order_id}`;
                break;
            case 'go_to_wallet':
                urlToOpen = '/wallet';
                break;
            case 'go_to_coupons':
                urlToOpen = '/coupons';
                break;
            case 'go_to_payments':
                urlToOpen = '/payments';
                break;
            case 'go_to_design_list':
                urlToOpen = '/designs';
                break;
            case 'go_to_design_order_details':
                urlToOpen = `/design-orders/${data.design_order_id}`;
                break;
        }
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // If a window is already open, focus it
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
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
