# 📱 Checklist - علشان الإشعارات تشتغل

## ✅ الأمور اللي لازم تتأكد منها:

### **1️⃣ Queue Worker شغال**

الإشعارات تشتغل عن طريق Queue، لازم تشغّل:

```bash
php artisan queue:work
```

**مهم:** خلي هذا الـ terminal مفتوح ويشتغل طول الوقت!

**للتحقق:**
```bash
php artisan queue:listen --tries=1
```

---

### **2️⃣ عندك Firebase Server Key في .env**

افتح ملف `.env` وتأكد من وجود:

```env
FIREBASE_SERVER_KEY=AAAA...xyz
```

**للحصول عليه:**
1. افتح [Firebase Console](https://console.firebase.google.com)
2. Project Settings → Cloud Messaging
3. انسخ **Server key**

---

### **3️⃣ المستخدم عنده FCM Token مسجل**

#### **أ) للمتصفح:**
- افتح `http://localhost:8000/firebase-test.html`
- سجّل دخول واحصل على token
- أو استخدم الكود في موقعك:

```javascript
const messaging = new FirebaseWebMessaging(firebaseConfig, 'http://localhost:8000');
await messaging.subscribe(authToken); // بعد تسجيل الدخول
```

#### **ب) للتحقق من Database:**
```sql
SELECT id, name, fcm_token FROM users;
```

إذا `fcm_token` فاضي (`NULL`)، معناها المستخدم ما سجّل للإشعارات.

---

### **4️⃣ اختبار الإشعارات**

#### **الطريقة 1: إنشاء Order حقيقي**
1. سجّل دخول كمستخدم (User A)
2. سجّل FCM token من المتصفح
3. سجّل دخول كمستخدم آخر (User B)
4. اطلب تصميم من المستخدم الأول (User A)
5. المستخدم الأول لازم يستلم إشعار!

#### **الطريقة 2: اختبار مباشر**

أضف في `routes/web.php`:

```php
use App\Models\User;
use App\Models\Order;
use App\Notifications\OrderCreatedNotification;

Route::get('/test-order-notification', function() {
    // المستخدم اللي بيستلم الإشعار
    $user = User::find(1); // غير الـ ID
    
    // تحقق من Token
    if (!$user->fcm_token) {
        return '❌ User does not have FCM token!<br>
                Register token first at: <a href="/firebase-test.html">Test Page</a>';
    }
    
    // أي order موجود
    $order = Order::first();
    
    if (!$order) {
        return '❌ No orders found in database!';
    }
    
    // إرسال الإشعار
    $user->notify(new OrderCreatedNotification($order));
    
    return "✅ Notification sent to {$user->name}!<br>
            Check your browser for notification.<br>
            Order ID: {$order->id}";
});
```

افتح: `http://localhost:8000/test-order-notification`

---

### **5️⃣ شوف الـ Logs**

```bash
# في Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 -Wait

# أو
tail -f storage/logs/laravel.log
```

ابحث عن:
- ✅ `Firebase notification sent successfully`
- ❌ `Firebase notification failed`

---

### **6️⃣ تحقق من Jobs Table**

```sql
SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
```

- إذا فيه jobs عالقة → Queue Worker مش شغال
- إذا الجدول فاضي → Jobs تنفذت بنجاح

---

## 🚨 المشاكل الشائعة:

### **❌ الإشعار ما وصل**

**السبب المحتمل:**
1. Queue Worker مش شغال
2. FCM token مش مسجل
3. Firebase Server Key غلط
4. المستخدم رفض إذن الإشعارات

**الحل:**
```bash
# 1. شغّل Queue Worker
php artisan queue:work

# 2. تحقق من الـ token
SELECT fcm_token FROM users WHERE id = 1;

# 3. تحقق من Server Key
# افتح .env وتأكد من FIREBASE_SERVER_KEY

# 4. شوف الـ logs
tail -f storage/logs/laravel.log
```

---

### **❌ Queue Worker يتوقف**

**الحل:**
```bash
# في Development
php artisan queue:listen --tries=1

# في Production - استخدم Supervisor
sudo apt install supervisor
```

---

### **❌ Permission denied في المتصفح**

**الحل:**
1. اذهب لإعدادات المتصفح
2. Site Settings → Notifications
3. فعّل الإشعارات للموقع

---

## ✨ الخلاصة - الأمور المطلوبة:

```bash
# 1. Queue Worker شغال
php artisan queue:work

# 2. Firebase Config صحيح
# تأكد من firebase-config.js

# 3. Server Key في .env
FIREBASE_SERVER_KEY=AAAA...

# 4. المستخدم سجّل Token
# افتح /firebase-test.html

# 5. اختبر
# افتح /test-order-notification
```

---

**إذا عملت كل شي صح، الإشعارات ستشتغل 100%! ✅**

بالتوفيق! 🚀
