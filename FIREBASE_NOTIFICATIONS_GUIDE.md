# Firebase Cloud Messaging (FCM) Integration Guide

## نظرة عامة / Overview

تم تطبيق نظام Firebase Cloud Messaging (FCM) لإرسال الإشعارات الفورية للمستخدمين.

## المكونات / Components

### 1. Database Migration
- **File**: `database/migrations/2026_02_04_000001_add_fcm_token_to_users_table.php`
- يضيف عمود `fcm_token` لجدول المستخدمين

### 2. Configuration
- **File**: `config/firebase.php`
- يحتوي على إعدادات Firebase:
  - `FIREBASE_SERVER_KEY`: مفتاح الخادم من Firebase Console

### 3. Firebase Service
- **File**: `app/Services/FirebaseService.php`
- يوفر طرق لإرسال الإشعارات:
  - `sendToDevice()`: إرسال لجهاز واحد
  - `sendToDevices()`: إرسال لعدة أجهزة
  - `sendToTopic()`: إرسال لموضوع معين

### 4. FCM Channel
- **File**: `app/Notifications/Channels/FcmChannel.php`
- قناة مخصصة للإشعارات عبر Firebase

### 5. Updated Notification Classes
جميع فئات الإشعارات تدعم الآن Firebase:
- `CouponCreatedNotification`
- `OrderCreatedNotification`
- `OrderStatusUpdatedNotification`
- `PaymentProcessedNotification`
- `WalletTransactionNotification`
- `DesignCreatedNotification`
- `DesignOrderStatusNotification`

كل فئة تحتوي على:
- `via()`: تتضمن `FcmChannel::class`
- `toFcm()`: تحدد محتوى الإشعار للـ Firebase

### 6. FCM Token Controller
- **File**: `app/Http/Controllers/FcmTokenController.php`
- API endpoints لإدارة FCM tokens

## الإعداد / Setup

### 1. تشغيل Migration
```bash
php artisan migrate
```

### 2. إضافة Firebase Server Key
أضف المفتاح في ملف `.env`:
```env
FIREBASE_SERVER_KEY=your_firebase_server_key_here
```

للحصول على Server Key:
1. اذهب إلى [Firebase Console](https://console.firebase.google.com)
2. اختر مشروعك
3. Project Settings > Cloud Messaging
4. انسخ "Server key"

## API Endpoints

### تسجيل FCM Token
```
POST /api/user/fcm/token
```
**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```
**Body:**
```json
{
  "fcm_token": "device_fcm_token_here"
}
```

### حذف FCM Token (Logout)
```
DELETE /api/user/fcm/token
```
**Headers:**
```
Authorization: Bearer {token}
```

## استخدام الإشعارات / Usage

عند إرسال أي إشعار، سيتم إرساله تلقائياً عبر:
1. **Database** - للعرض داخل التطبيق
2. **Email** - البريد الإلكتروني (للإشعارات المحددة)
3. **FCM** - إشعارات فورية للجوال

### مثال على بنية الإشعار FCM

```php
public function toFcm(object $notifiable): array
{
    return [
        'notification' => [
            'title' => 'عنوان الإشعار',
            'body' => 'نص الإشعار',
            'sound' => 'default',
        ],
        'data' => [
            'type' => 'notification_type',
            'id' => 'related_id',
            'action' => 'action_to_perform',
        ],
    ];
}
```

## بنية Data في الإشعارات / Notification Data Structure

### أنواع الإشعارات والـ Actions:

1. **Coupon Created**
   - `type`: `coupon_created`
   - `action`: `go_to_coupons`

2. **Order Created**
   - `type`: `order_created`
   - `action`: `go_to_order_list`

3. **Order Status Updated**
   - `type`: `order_status_updated`
   - `action`: `go_to_order_details`

4. **Payment Processed**
   - `type`: `payment_processed`
   - `action`: `go_to_payments`

5. **Wallet Transaction**
   - `type`: `wallet_transaction`
   - `action`: `go_to_wallet`

6. **Design Created**
   - `type`: `design_created`
   - `action`: `go_to_design_list`

7. **Design Order Status**
   - `type`: `design_order_status`
   - `action`: `go_to_design_order_details`

## التطبيق على الموبايل / Mobile App Implementation

### Flutter Example

```dart
// تسجيل FCM Token
Future<void> registerFcmToken() async {
  final fcmToken = await FirebaseMessaging.instance.getToken();
  
  final response = await http.post(
    Uri.parse('https://your-api.com/api/user/fcm/token'),
    headers: {
      'Authorization': 'Bearer $authToken',
      'Content-Type': 'application/json',
    },
    body: jsonEncode({'fcm_token': fcmToken}),
  );
}

// معالجة الإشعارات
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  print('Got a message: ${message.notification?.title}');
  
  // استخراج البيانات
  final data = message.data;
  final action = data['action'];
  
  // التوجيه بناءً على الـ action
  switch(action) {
    case 'go_to_order_details':
      navigateToOrderDetails(data['order_id']);
      break;
    case 'go_to_wallet':
      navigateToWallet();
      break;
    // ... إلخ
  }
});
```

### React Native Example

```javascript
import messaging from '@react-native-firebase/messaging';

// تسجيل FCM Token
async function registerFcmToken() {
  const fcmToken = await messaging().getToken();
  
  await fetch('https://your-api.com/api/user/fcm/token', {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${authToken}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({ fcm_token: fcmToken }),
  });
}

// معالجة الإشعارات
messaging().onMessage(async remoteMessage => {
  const { notification, data } = remoteMessage;
  
  // التعامل مع الـ action
  switch(data.action) {
    case 'go_to_order_list':
      navigation.navigate('OrderList');
      break;
    case 'go_to_wallet':
      navigation.navigate('Wallet');
      break;
  }
});
```

## الأمان / Security

- FCM tokens يتم تخزينها بشكل آمن في قاعدة البيانات
- يتم حذف الـ token تلقائياً عند تسجيل الخروج
- جميع API endpoints محمية بـ authentication

## ملاحظات مهمة / Important Notes

1. تأكد من تفعيل Firebase Cloud Messaging في Firebase Console
2. قم بتحديث FCM token عند تسجيل الدخول
3. احذف FCM token عند تسجيل الخروج
4. معالجة الإشعارات في التطبيق بناءً على `action` و `type`

## استكشاف الأخطاء / Troubleshooting

### الإشعارات لا تصل؟

1. تحقق من `FIREBASE_SERVER_KEY` في `.env`
2. تأكد من تسجيل FCM token بنجاح
3. تحقق من الـ logs في `storage/logs/laravel.log`
4. تأكد من تفعيل Cloud Messaging API في Firebase Console

### خطأ في الإرسال؟

```bash
# تحقق من logs
tail -f storage/logs/laravel.log

# تأكد من Queue worker يعمل
php artisan queue:work
```
