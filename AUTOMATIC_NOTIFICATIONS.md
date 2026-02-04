# الإشعارات التلقائية - Automatic Notifications

## ✅ الإشعارات التي تعمل تلقائياً

تم إضافة الإشعارات التلقائية في الأماكن التالية:

### 1️⃣ عند إنشاء تصميم جديد (Design Created)
**المكان**: `DesignService::createDesign()`  
**الملف**: `app/Http/Services/Api/DesignService.php`

```php
// يتم إرسال إشعار تلقائياً للأدمن عند إنشاء تصميم جديد
$admins = User::role('admin')->get();
foreach ($admins as $admin) {
    $admin->notify(new DesignCreatedNotification($design));
}
```

**المستلمون**: Admin فقط  
**القناة**: Database  
**العنوان**: New Design Created  
**النص**: A new design has been created by a user. Tap to review it in the design list  
**الإجراء**: go_to_design_list

---

### 2️⃣ عند إنشاء طلب جديد (Order Created)
**المكان**: `OrderService::add_Design()`  
**الملف**: `app/Http/Services/Api/OrderService.php`

```php
// يتم إرسال إشعار تلقائياً عند إنشاء طلب جديد لأول مرة
if ($isNewOrder) {
    // إرسال إشعار لصاحب التصميم
    $design->user->notify(new OrderCreatedNotification($order));
    
    // إرسال إشعار للأدمن
    $admins = User::role('admin')->get();
    foreach ($admins as $admin) {
        $admin->notify(new OrderCreatedNotification($order));
    }
}
```

**المستلمون**: Admin + صاحب التصميم (User who created the design)  
**القناة**: Database  
**العنوان**: New Order Created  
**النص**: A new order has been placed for your design. Tap to view the order details.  
**الإجراء**: go_to_order_list

---

### 3️⃣ عند تحديث حالة الطلب (Order Status Updated)
**المكان**: `OrderService::cancelOrder()` و `PaymentService`  
**الملف**: `app/Http/Services/Api/OrderService.php` & `PaymentService.php`

```php
// يتم إرسال إشعار تلقائياً عند تحديث حالة الطلب
$oldStatus = $order->status;
$order->status = OrderStatusEnum::Cancelled;
$order->save();
$order->user->notify(new OrderStatusUpdatedNotification($order, $oldStatus));
```

**المستلمون**: User (صاحب الطلب)  
**القناة**: Database  
**العنوان**: Order Status Updated  
**النص**: The status of your order #{order_id} has been updated to {new_status}. Tap to view details

**متى يُرسل**: 
- عند إلغاء الطلب من قبل المستخدم
- عند إتمام الدفع (تتغير الحالة من pending إلى processing)

---

## 🔔 كيف تعمل الإشعارات

### 1. تلقائياً (Automatic)
جميع الإشعارات المذكورة أعلاه تُرسل **تلقائياً** بدون الحاجة لأي تدخل يدوي.

### 2. عبر Queue (Queued)
جميع الإشعارات تطبق `ShouldQueue` مما يعني:
- لا تبطئ عملية الطلب/الدفع
- تُرسل في الخلفية (background)
- تحتاج لتشغيل queue worker:
```bash
php artisan queue:work
```

### 3. قناة واحدة (Database Only)
كل إشعار يُرسل عبر قناة واحدة فقط:
- **Database**: يُحفظ في جدول `notifications`
- تم إزالة Email لتحسين الأداء

---

## 🧪 اختبار الإشعارات (Testing)

### 1. اختبار إنشاء تصميم:
```bash
POST /api/user/design/create
```
**النتيجة**: سيُرسل إشعار `DesignCreatedNotification` للأدمن تلقائياً

### 2. اختبار إنشاء طلب:
```bash
POST /api/user/order/create
```
**النتيجة**: سيُرسل إشعار `OrderCreatedNotification` لصاحب التصميم والأدمن تلقائياً

### 3. اختبار إلغاء طلب:
```bash
POST /api/user/order/cancel/{order}
```
**النتيجة**: سيُرسل إشعار `OrderStatusUpdatedNotification` للمستخدم تلقائياً

### 4. اختبار الدفع:
```bash
POST /api/user/payment/wallet/{order}
# أو عند نجاح Stripe webhook
```
**النتيجة**: سيُرسل إشعار `OrderStatusUpdatedNotification` للمستخدم

### 5. عرض الإشعارات:
```bash
GET /api/user/notifications
GET /api/user/notifications/unread
GET /api/user/notifications/count
```

---

## ⚙️ متطلبات التشغيل

### 1. تشغيل Queue Worker
```bash
php artisan queue:work
```

### 2. تشغيل Migration
```bash
php artisan migrate
```

### 3. التأكد من وجود Roles
تأكد من وجود role "admin" في النظام:
```php
use Spatie\Permission\Models\Role;

Role::create(['name' => 'admin']);
```

---

## 📊 ملخص الإشعارات

| الحدث | المستلمون | العنوان | القناة | تلقائي؟ |
|------|-----------|---------|--------|---------|
| **إنشاء تصميم** | Admin | New Design Created | DB | ✅ |
| **إنشاء طلب** | Admin + صاحب التصميم | New Order Created | DB | ✅ |
| **تحديث حالة طلب** | User (صاحب الطلب) | Order Status Updated | DB | ✅ |

---

## 🎯 الخلاصة

**نعم، الإشعارات تعمل تلقائياً الآن!** 🎉

✅ **عند إنشاء تصميم** → الأدمن يستلم إشعار  
✅ **عند إنشاء طلب** → الأدمن وصاحب التصميم يستلمون إشعار  
✅ **عند تغيير حالة الطلب** → صاحب الطلب يستلم إشعار

### الإشعارات المحذوفة:
❌ PaymentProcessedNotification (تم دمجها مع OrderStatusUpdated)  
❌ WalletTransactionNotification  
❌ CouponCreatedNotification  
❌ DesignOrderStatusNotification

فقط تأكد من تشغيل `queue:work` لمعالجة الإشعارات في الخلفية.
