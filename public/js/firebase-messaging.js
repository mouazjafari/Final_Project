// Firebase Messaging Helper for Web
// Include this in your main JavaScript file

class FirebaseWebMessaging {
    constructor(firebaseConfig, apiUrl) {
        this.apiUrl = apiUrl;
        this.firebaseConfig = firebaseConfig;
        this.messaging = null;
        this.currentToken = null;
    }

    /**
     * Initialize Firebase
     */
    async init() {
        try {
            // Check if Firebase is supported
            if (!firebase) {
                throw new Error('Firebase SDK not loaded');
            }

            // Initialize Firebase app
            if (!firebase.apps.length) {
                firebase.initializeApp(this.firebaseConfig);
            }

            // Get messaging instance
            this.messaging = firebase.messaging();

            console.log('✅ Firebase initialized successfully');
            return true;
        } catch (error) {
            console.error('❌ Firebase initialization failed:', error);
            throw error;
        }
    }

    /**
     * Request notification permission
     */
    async requestPermission() {
        try {
            const permission = await Notification.requestPermission();

            if (permission === 'granted') {
                console.log('✅ Notification permission granted');
                return true;
            } else {
                console.log('❌ Notification permission denied');
                return false;
            }
        } catch (error) {
            console.error('Error requesting permission:', error);
            throw error;
        }
    }

    /**
     * Get FCM token
     */
    async getToken() {
        try {
            if (!this.messaging) {
                await this.init();
            }

            // Register service worker
            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('Service Worker registered:', registration);

            // Get token
            const token = await this.messaging.getToken({
                vapidKey: 'BBVW7_P9tJaeN8HU6jnPSIV4f4CNMgwEJW9ithNQfRRwDpa5404WW3PQ1-hdHpZuIh-AieHkadya8vXWkowLQiY', // Add this from Firebase Console
                serviceWorkerRegistration: registration
            });

            if (token) {
                this.currentToken = token;
                console.log('✅ FCM Token:', token);
                return token;
            } else {
                console.log('❌ No registration token available');
                return null;
            }
        } catch (error) {
            console.error('Error getting token:', error);
            throw error;
        }
    }

    /**
     * Subscribe to notifications (register token with backend)
     */
    async subscribe(authToken) {
        try {
            // Request permission
            const hasPermission = await this.requestPermission();
            if (!hasPermission) {
                throw new Error('Notification permission not granted');
            }

            // Get FCM token
            const fcmToken = await this.getToken();
            if (!fcmToken) {
                throw new Error('Failed to get FCM token');
            }

            // Send token to backend
            const response = await fetch(`${this.apiUrl}/api/user/fcm/token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${authToken}`
                },
                body: JSON.stringify({ fcm_token: fcmToken })
            });

            if (!response.ok) {
                throw new Error('Failed to register FCM token with backend');
            }

            const data = await response.json();
            console.log('✅ FCM token registered with backend:', data);

            // Setup foreground message handler
            this.setupForegroundHandler();

            // Setup token refresh handler
            this.setupTokenRefreshHandler(authToken);

            return fcmToken;
        } catch (error) {
            console.error('❌ Subscription failed:', error);
            throw error;
        }
    }

    /**
     * Unsubscribe from notifications
     */
    async unsubscribe(authToken) {
        try {
            if (!this.messaging) {
                await this.init();
            }

            // Delete token from Firebase
            if (this.currentToken) {
                await this.messaging.deleteToken();
                console.log('✅ Token deleted from Firebase');
            }

            // Remove token from backend
            const response = await fetch(`${this.apiUrl}/api/user/fcm/token`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${authToken}`
                }
            });

            if (response.ok) {
                console.log('✅ Token removed from backend');
                this.currentToken = null;
            }
        } catch (error) {
            console.error('❌ Unsubscribe failed:', error);
            throw error;
        }
    }

    /**
     * Setup foreground message handler
     */
    setupForegroundHandler() {
        if (!this.messaging) return;

        this.messaging.onMessage((payload) => {
            console.log('📬 Message received (foreground):', payload);

            const { notification, data } = payload;

            // Show notification using browser API
            if (notification) {
                const notificationTitle = notification.title || 'New Notification';
                const notificationOptions = {
                    body: notification.body || '',
                    icon: notification.icon || '/images/notification-icon.png',
                    badge: '/images/notification-badge.png',
                    data: data || {},
                    tag: data?.type || 'notification',
                    requireInteraction: false
                };

                if (Notification.permission === 'granted') {
                    new Notification(notificationTitle, notificationOptions);
                }
            }

            // Handle custom actions
            if (data && data.action) {
                this.handleNotificationAction(data);
            }
        });
    }

    /**
     * Setup token refresh handler
     */
    setupTokenRefreshHandler(authToken) {
        if (!this.messaging) return;

        // Handle token refresh
        this.messaging.onTokenRefresh(async () => {
            try {
                const newToken = await this.getToken();
                if (newToken) {
                    // Update token on backend
                    await fetch(`${this.apiUrl}/api/user/fcm/token`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${authToken}`
                        },
                        body: JSON.stringify({ fcm_token: newToken })
                    });
                    console.log('✅ Token refreshed and updated');
                }
            } catch (error) {
                console.error('❌ Token refresh failed:', error);
            }
        });
    }

    /**
     * Handle notification actions (optional - customize based on your app)
     */
    handleNotificationAction(data) {
        console.log('Handling notification action:', data);

        // You can add custom logic here
        // For example, navigate to specific page, update UI, etc.
    }

    /**
     * Set custom message handler for foreground messages
     */
    onMessage(callback) {
        if (!this.messaging) {
            console.error('Firebase messaging not initialized');
            return;
        }

        this.messaging.onMessage((payload) => {
            console.log('📬 Message received (foreground):', payload);
            callback(payload);
        });
    }
}

// Usage Example:
/*
<!-- In your HTML, load Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-messaging-compat.js"></script>
<script src="/js/firebase-config.js"></script>
<script src="/js/firebase-messaging.js"></script>

<script>
// Initialize
const messaging = new FirebaseWebMessaging(firebaseConfig, 'http://your-api-url.com');

// On user login
async function handleLogin(authToken) {
    try {
        await messaging.subscribe(authToken);
        console.log('✅ Subscribed to Firebase notifications');
    } catch (error) {
        console.error('❌ Failed to subscribe:', error);
    }
}

// On user logout
async function handleLogout(authToken) {
    try {
        await messaging.unsubscribe(authToken);
        console.log('✅ Unsubscribed from Firebase notifications');
    } catch (error) {
        console.error('❌ Failed to unsubscribe:', error);
    }
}
</script>
*/
