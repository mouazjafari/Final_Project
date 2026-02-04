# نظام الإشعارات (Notifications System)

## نظرة عامة / Overview

تم إنشاء نظام إشعارات شامل في المشروع يدعم إرسال الإشعارات عبر قنوات متعددة (قاعدة البيانات، البريد الإلكتروني).

## المكونات / Components

### 1. Migration (قاعدة البيانات)
- **File**: `database/migrations/2026_01_28_204724_create_notification_table.php`
- يحتوي على جدول `notifications` الذي يخزن جميع الإشعارات

### 2. Notification Classes (فئات الإشعارات)

#### OrderCreatedNotification
- **File**: `app/Notifications/OrderCreatedNotification.php`
- يُستخدم عند إنشاء طلب جديد
- يرسل عبر: Database, Email

#### OrderStatusUpdatedNotification
- **File**: `app/Notifications/OrderStatusUpdatedNotification.php`
- يُستخدم عند تحديث حالة الطلب
- يرسل عبر: Database, Email

#### PaymentProcessedNotification
- **File**: `app/Notifications/PaymentProcessedNotification.php`
- يُستخدم عند معالجة دفعة
- يرسل عبر: Database, Email

#### DesignOrderStatusNotification
- **File**: `app/Notifications/DesignOrderStatusNotification.php`
- يُستخدم عند تحديث حالة طلب التصميم
- يرسل عبر: Database, Email

#### CouponCreatedNotification
- **File**: `app/Notifications/CouponCreatedNotification.php`
- يُستخدم عند إنشاء كوبون جديد
- يرسل عبر: Database, Email

#### WalletTransactionNotification
- **File**: `app/Notifications/WalletTransactionNotification.php`
- يُستخدم عند إجراء معاملة في المحفظة
- يرسل عبر: Database, Email

### 3. NotificationController
- **File**: `app/Http/Controllers/NotificationController.php`
- يحتوي على جميع methods لإدارة الإشعارات

### 4. NotificationResource
- **File**: `app/Http/Resources/NotificationResource.php`
- يقوم بتحويل بيانات الإشعارات إلى JSON

## API Endpoints

### 1. Get All Notifications (جلب جميع الإشعارات)
```
GET /api/user/notifications
```
**Parameters:**
- `per_page` (optional): عدد الإشعارات في الصفحة (الافتراضي: 15)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": "uuid",
            "type": "App\\Notifications\\OrderCreatedNotification",
            "data": {
                "order_id": 1,
                "message": "Your order #1 has been created successfully."
            },
            "read_at": null,
            "created_at": "2026-01-28 10:00:00",
            "is_read": false,
            "time_ago": "2 hours ago"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

### 2. Get Unread Notifications (جلب الإشعارات غير المقروءة)
```
GET /api/user/notifications/unread
```
**Parameters:**
- `per_page` (optional): عدد الإشعارات في الصفحة

**Response:**
```json
{
    "success": true,
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "per_page": 15,
        "total": 25,
        "unread_count": 25
    }
}
```

### 3. Get Notifications Count (جلب عدد الإشعارات)
```
GET /api/user/notifications/count
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total": 75,
        "unread": 25,
        "read": 50
    }
}
```

### 4. Mark Notification as Read (تحديد إشعار كمقروء)
```
POST /api/user/notifications/{id}/read
```

**Response:**
```json
{
    "success": true,
    "message": "Notification marked as read",
    "data": {
        "id": "uuid",
        "type": "...",
        "read_at": "2026-01-28 12:00:00",
        "is_read": true
    }
}
```

### 5. Mark All Notifications as Read (تحديد جميع الإشعارات كمقروءة)
```
POST /api/user/notifications/read-all
```

**Response:**
```json
{
    "success": true,
    "message": "All notifications marked as read"
}
```

### 6. Delete Notification (حذف إشعار)
```
DELETE /api/user/notifications/{id}
```

**Response:**
```json
{
    "success": true,
    "message": "Notification deleted successfully"
}
```

### 7. Delete All Notifications (حذف جميع الإشعارات)
```
DELETE /api/user/notifications
```

**Response:**
```json
{
    "success": true,
    "message": "All notifications deleted successfully"
}
```

## كيفية الاستخدام / Usage

### في Controllers

#### إرسال إشعار عند إنشاء طلب جديد:
```php
use App\Notifications\OrderCreatedNotification;

public function create(Request $request) {
    $order = Order::create($request->all());
    
    // إرسال إشعار للمستخدم
    $request->user()->notify(new OrderCreatedNotification($order));
    
    return response()->json(['order' => $order]);
}
```

#### إرسال إشعار عند تحديث حالة الطلب:
```php
use App\Notifications\OrderStatusUpdatedNotification;

public function updateStatus(Request $request, Order $order) {
    $oldStatus = $order->status;
    $order->update(['status' => $request->status]);
    
    // إرسال إشعار
    $order->user->notify(new OrderStatusUpdatedNotification($order, $oldStatus));
    
    return response()->json(['order' => $order]);
}
```

#### إرسال إشعار عند معالجة الدفعة:
```php
use App\Notifications\PaymentProcessedNotification;

public function processPayment(Request $request, Order $order) {
    $payment = Payment::create([...]);
    
    // إرسال إشعار
    $order->user->notify(new PaymentProcessedNotification($payment));
    
    return response()->json(['payment' => $payment]);
}
```

#### إرسال إشعار لعدة مستخدمين:
```php
use App\Notifications\CouponCreatedNotification;

public function createCoupon(Request $request) {
    $coupon = Coupon::create($request->all());
    
    // إرسال إشعار لجميع المستخدمين
    $users = User::all();
    foreach ($users as $user) {
        $user->notify(new CouponCreatedNotification($coupon));
    }
    
    return response()->json(['coupon' => $coupon]);
}
```

## Queue Configuration (تكوين قائمة الانتظار)

جميع الإشعارات تطبق `ShouldQueue` مما يعني أنها تُرسل بشكل غير متزامن (asynchronous).

لتشغيل queue worker:
```bash
php artisan queue:work
```

## Mail Configuration (تكوين البريد الإلكتروني)

تأكد من تكوين إعدادات البريد الإلكتروني في `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

## تشغيل Migration

لإنشاء جدول الإشعارات في قاعدة البيانات:
```bash
php artisan migrate
```

## ملاحظات مهمة / Important Notes

1. جميع الإشعارات تُرسل عبر قناتين: Database و Mail
2. يمكن تعديل القنوات في method `via()` في كل notification class
3. يمكن إضافة قنوات أخرى مثل SMS أو Push Notifications
4. الإشعارات تعمل بشكل غير متزامن (queued) لتحسين الأداء
5. يمكن تخصيص محتوى البريد الإلكتروني في method `toMail()`
6. يمكن تخصيص البيانات المخزنة في قاعدة البيانات في method `toArray()`

## أمثلة إضافية / Additional Examples

يمكنك الاطلاع على ملف الأمثلة للمزيد من الحالات:
- **File**: `app/Examples/NotificationExamples.php`

## الدعم / Support

لمزيد من المعلومات، راجع توثيق Laravel الرسمي:
- [Laravel Notifications Documentation](https://laravel.com/docs/notifications)
