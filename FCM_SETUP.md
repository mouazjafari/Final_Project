# Firebase Cloud Messaging (FCM) - Push Notifications

## نظرة عامة | Overview

نظام إشعارات Push باستخدام Firebase Cloud Messaging للويب والموبايل.

---

## البنية المعمارية | Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Frontend                                 │
│  ┌─────────────────┐    ┌─────────────────┐                     │
│  │ firebase-config │    │ firebase-sw.js  │                     │
│  │      .js        │    │ (Service Worker)│                     │
│  └────────┬────────┘    └────────┬────────┘                     │
│           │                      │                               │
│           └──────────┬───────────┘                               │
│                      ▼                                           │
│            ┌─────────────────┐                                   │
│            │ firebase-       │                                   │
│            │ messaging.js    │                                   │
│            └────────┬────────┘                                   │
│                     │ FCM Token                                  │
└─────────────────────┼───────────────────────────────────────────┘
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Backend                                  │
│  ┌─────────────────┐    ┌─────────────────┐                     │
│  │ AuthController  │───▶│ users.fcm_token │                     │
│  │ (save token)    │    │   (Database)    │                     │
│  └─────────────────┘    └─────────────────┘                     │
│                                                                  │
│  ┌─────────────────┐    ┌─────────────────┐                     │
│  │  Notification   │───▶│   FcmChannel    │                     │
│  │   (Laravel)     │    │                 │                     │
│  └─────────────────┘    └────────┬────────┘                     │
│                                  │                               │
│                                  ▼                               │
│                         ┌─────────────────┐                     │
│                         │ FirebaseService │                     │
│                         │ (Admin SDK)     │                     │
│                         └────────┬────────┘                     │
│                                  │                               │
└──────────────────────────────────┼──────────────────────────────┘
                                   ▼
                          ┌─────────────────┐
                          │  Firebase Cloud │
                          │    Messaging    │
                          └────────┬────────┘
                                   │
                                   ▼
                          ┌─────────────────┐
                          │  User's Browser │
                          │  (Notification) │
                          └─────────────────┘
```

---

## الملفات | Files

### Backend (Laravel)

| الملف | الوظيفة |
|-------|---------|
| `app/Services/FirebaseService.php` | إرسال الإشعارات عبر Firebase Admin SDK |
| `app/Notifications/Channels/FcmChannel.php` | قناة Laravel للإشعارات |
| `app/Http/Controllers/Web/Admin/AuthController.php` | حفظ FCM Token |
| `storage/app/firebase/firebase_credentials.json` | مفاتيح Firebase (سري) |
| `config/firebase.php` | إعدادات Firebase |

### Frontend (JavaScript)

| الملف | الوظيفة |
|-------|---------|
| `public/firebase-messaging-sw.js` | Service Worker - استقبال الإشعارات في الخلفية |
| `public/js/firebase-config.js` | إعدادات Firebase للمتصفح |
| `public/js/firebase-messaging.js` | Class للتعامل مع FCM |

### Views

| الملف | الوظيفة |
|-------|---------|
| `resources/views/admin/layouts/admin-layout.blade.php` | تضمين Firebase وتهيئته |

---

## كيف تعمل الإشعارات | How It Works

### 1. تسجيل المستخدم للإشعارات

```javascript
// عند تحميل الصفحة في admin-layout.blade.php
const firebaseMessaging = new FirebaseWebMessaging(firebaseConfig, apiUrl);
await firebaseMessaging.init();                    // تهيئة Firebase
await firebaseMessaging.requestPermission();       // طلب إذن الإشعارات
const token = await firebaseMessaging.getToken();  // الحصول على FCM Token

// إرسال Token للـ Backend
fetch('/admin/update-fcm-token', {
    method: 'POST',
    body: JSON.stringify({ fcm_token: token })
});
```

### 2. حفظ Token في قاعدة البيانات

```php
// AuthController.php
public function updateFcmToken(Request $request)
{
    $user = Auth::user();
    $user->update(['fcm_token' => $request->fcm_token]);
}
```

### 3. إرسال الإشعار (عند إنشاء Design مثلاً)

```php
// DesignObserver.php
public function created(Design $design)
{
    // إرسال إشعار لجميع الـ Admins
    $admins = User::role('admin')->get();
    Notification::send($admins, new DesignCreatedNotification($design));
}
```

### 4. معالجة الإشعار عبر FcmChannel

```php
// FcmChannel.php
public function send($notifiable, Notification $notification)
{
    $token = $notifiable->fcm_token;           // جلب Token المستخدم
    $message = $notification->toFcm($notifiable); // تحضير الرسالة
    
    $this->firebase->sendToDevice($token, $message['notification'], $message['data']);
}
```

### 5. إرسال عبر Firebase Admin SDK

```php
// FirebaseService.php
public function sendToDevice(string $token, array $notification, array $data = []): bool
{
    $message = CloudMessage::withTarget('token', $token)
        ->withNotification(Notification::create($notification['title'], $notification['body']))
        ->withData($data);

    $this->messaging->send($message);  // إرسال لـ Firebase
}
```

### 6. استقبال الإشعار في المتصفح

```javascript
// firebase-messaging-sw.js (Service Worker)
messaging.onBackgroundMessage((payload) => {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/images/notification-icon.png'
    });
});
```

---

## الإشعارات المدعومة | Supported Notifications

| الإشعار | الحدث | المستلم |
|---------|-------|---------|
| `DesignCreatedNotification` | إنشاء تصميم جديد | Admins |
| `OrderCreatedNotification` | إنشاء طلب جديد | Admin + صاحب التصميم |
| `OrderStatusUpdatedNotification` | تحديث حالة الطلب | صاحب الطلب |
| `DesignOrderStatusNotification` | تحديث طلب التصميم | صاحب الطلب |
| `PaymentProcessedNotification` | معالجة الدفع | صاحب الدفعة |
| `WalletTransactionNotification` | عملية على المحفظة | صاحب المحفظة |
| `CouponCreatedNotification` | إنشاء كوبون | المستخدمين |

---

## الإعداد | Setup

### 1. Firebase Console

1. اذهب لـ [Firebase Console](https://console.firebase.google.com/)
2. أنشئ مشروع أو اختر موجود
3. **Project Settings** → **Service accounts** → **Generate new private key**
4. احفظ الملف في: `storage/app/firebase/firebase_credentials.json`
5. **Project Settings** → **General** → **Your apps** → أضف Web App
6. انسخ الـ Config (apiKey, projectId, etc.)
7. **Cloud Messaging** → **Web Push certificates** → **Generate key pair** (VAPID Key)

### 2. تحديث ملفات الـ Frontend

**public/firebase-messaging-sw.js** و **public/js/firebase-config.js**:
```javascript
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_PROJECT_ID.firebaseapp.com",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_PROJECT_ID.appspot.com",
    messagingSenderId: "YOUR_SENDER_ID",
    appId: "YOUR_APP_ID"
};
```

**public/js/firebase-messaging.js** (السطر 73):
```javascript
vapidKey: 'YOUR_VAPID_KEY_FROM_FIREBASE_CONSOLE'
```

### 3. تشغيل Queue Worker

```bash
php artisan queue:work
```

---

## استكشاف الأخطاء | Troubleshooting

### الإشعار لا يظهر

1. **تحقق من Console المتصفح (F12)**:
   - `✅ Firebase initialized successfully`
   - `✅ Notification permission granted`
   - `✅ FCM Token: ...`

2. **تحقق من قاعدة البيانات**:
   ```sql
   SELECT id, name, fcm_token FROM users WHERE fcm_token IS NOT NULL;
   ```

3. **تحقق من Laravel Log**:
   ```bash
   Get-Content storage/logs/laravel.log -Tail 20
   ```

### أخطاء شائعة

| الخطأ | السبب | الحل |
|-------|-------|------|
| `Firebase credentials file not found` | ملف credentials غير موجود | ضع الملف في `storage/app/firebase/firebase_credentials.json` |
| `Invalid VAPID key` | VAPID Key غير صحيح | انسخ من Firebase Console → Cloud Messaging |
| `404 fcm.googleapis.com` | Legacy API (قديم) | استخدم Firebase Admin SDK (تم التحديث) |
| `Firebase messaging not initialized` | أعد تشغيل Queue | `php artisan queue:restart` |

---

## الـ Packages المستخدمة | Dependencies

```json
{
    "kreait/laravel-firebase": "^6.2"
}
```

---

## ملاحظات مهمة | Important Notes

1. **HTTPS مطلوب** - Push Notifications تعمل فقط على HTTPS (أو localhost)
2. **Queue Worker** - يجب تشغيله لإرسال الإشعارات: `php artisan queue:work`
3. **Service Worker** - يجب أن يكون في root الموقع (`/firebase-messaging-sw.js`)
4. **إذن المستخدم** - يجب أن يوافق المستخدم على الإشعارات
5. **FCM Token** - يتغير أحياناً، يتم تحديثه تلقائياً

---

## الملفات المحذوفة (كانت Web Push) | Removed Files

تم إزالة نظام Web Push/VAPID القديم واستبداله بـ Firebase:

- ~~config/webpush.php~~
- ~~app/Services/WebPushService.php~~
- ~~app/Notifications/Channels/WebPushChannel.php~~
- ~~app/Models/PushSubscription.php~~
- ~~app/Http/Controllers/PushSubscriptionController.php~~
- ~~public/js/webpush.js~~
