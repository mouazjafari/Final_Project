# Dashboard Access Permission - دليل صلاحية الوصول إلى لوحة التحكم

## نظرة عامة
تم إضافة صلاحية جديدة للتحكم في من يمكنه الوصول إلى لوحة التحكم (Dashboard) في التطبيق.

## الصلاحية الجديدة
- **اسم الصلاحية**: `access dashboard`
- **Guard**: `web`
- **الوصف**: تسمح للمستخدمين بالوصول إلى لوحة التحكم الرئيسية

## الملفات المُعدّلة

### 1. PermissionSeeder.php
تم إضافة صلاحية `access dashboard` إلى قائمة الصلاحيات لـ `web guard` وإعطائها تلقائياً لدور `Admin` و `SuperAdmin`.

### 2. DashboardAccessMiddleware.php (جديد)
Middleware جديد للتحقق من صلاحية الوصول إلى Dashboard:
- يتحقق من تسجيل دخول المستخدم
- يتحقق من امتلاك المستخدم لصلاحية `access dashboard`
- يعيد التوجيه لصفحة تسجيل الدخول في حالة عدم وجود الصلاحية

### 3. bootstrap/app.php
تم تسجيل الـ Middleware الجديد بالاسم: `dashboard.access`

### 4. routes/web.php
تم تطبيق الـ Middleware على route الـ Dashboard

## كيفية الاستخدام

### 1. تشغيل الـ Seeder
```bash
php artisan db:seed --class=PermissionSeeder
```

أو إعادة تشغيل جميع الـ Seeders:
```bash
php artisan migrate:fresh --seed
```

### 2. إعطاء صلاحية Dashboard لمستخدم معين
```php
use App\Models\User;

$user = User::find(1);
$user->givePermissionTo('access dashboard', 'web');
```

### 3. إعطاء صلاحية Dashboard لدور (Role)
```php
use Spatie\Permission\Models\Role;

$role = Role::findByName('Manager', 'web');
$role->givePermissionTo('access dashboard');
```

### 4. إنشاء دور جديد مع صلاحية Dashboard
```php
use Spatie\Permission\Models\Role;

$customRole = Role::create(['name' => 'Moderator', 'guard_name' => 'web']);
$customRole->givePermissionTo([
    'access dashboard',
    'view orders',
    'view designs'
]);
```

### 5. التحقق من الصلاحية في Controller
```php
// في Controller
public function index()
{
    // الـ Middleware سيتحقق تلقائياً
    // لكن يمكنك التحقق يدوياً أيضاً:
    
    if (auth()->user()->can('access dashboard')) {
        // المستخدم لديه صلاحية
    }
    
    return view('admin.dashboard');
}
```

### 6. التحقق من الصلاحية في Blade Templates
```php
@can('access dashboard')
    <a href="{{ route('admin.dashboard') }}">لوحة التحكم</a>
@endcan
```

## الأدوار الحالية والصلاحيات

### SuperAdmin (web)
- يمتلك **جميع** الصلاحيات تلقائياً بما فيها `access dashboard`

### Admin (web)
- يمتلك صلاحية `access dashboard`
- يمتلك معظم صلاحيات الإدارة

### User (api)
- **لا** يمتلك صلاحية `access dashboard`
- فقط صلاحيات API للمستخدمين العاديين

## ملاحظات مهمة

1. **Guard مختلف**: 
   - صلاحيات `web` للوحة التحكم
   - صلاحيات `api` للتطبيق

2. **الأمان**: 
   - الـ Middleware يقوم بتسجيل خروج المستخدم تلقائياً إذا لم يملك الصلاحية
   - يتم إعادة توجيهه لصفحة تسجيل الدخول مع رسالة خطأ

3. **التوسع المستقبلي**:
   - يمكن إضافة صلاحيات فرعية أخرى للـ Dashboard
   - يمكن إنشاء أدوار مخصصة حسب الحاجة

## أمثلة عملية

### مثال 1: إنشاء مدير للطلبات فقط
```php
$ordersManager = Role::create(['name' => 'Orders Manager', 'guard_name' => 'web']);
$ordersManager->givePermissionTo([
    'access dashboard',
    'view orders',
    'change status orders'
]);

// إعطاء الدور لمستخدم
$user = User::find(5);
$user->assignRole('Orders Manager');
```

### مثال 2: إنشاء مدير للتصاميم
```php
$designsManager = Role::create(['name' => 'Designs Manager', 'guard_name' => 'web']);
$designsManager->givePermissionTo([
    'access dashboard',
    'view designs',
    'edit designs',
    'delete designs',
    'view design options'
]);
```

### مثال 3: إزالة صلاحية Dashboard من مستخدم
```php
$user = User::find(10);
$user->revokePermissionTo('access dashboard');
// الآن لن يستطيع الوصول للـ Dashboard
```

## استكشاف الأخطاء

### المشكلة: "ليس لديك صلاحية الوصول إلى لوحة التحكم"
**الحل**:
```php
// تحقق من صلاحيات المستخدم
$user = User::find(YOUR_USER_ID);
dd($user->getAllPermissions());

// أعطِ الصلاحية
$user->givePermissionTo('access dashboard', 'web');
```

### المشكلة: الصلاحيات لا تعمل بعد تحديث Seeder
**الحل**:
```bash
# امسح الـ cache
php artisan cache:clear
php artisan config:clear

# أعد تشغيل الـ Seeder
php artisan db:seed --class=PermissionSeeder
```

## الخلاصة
الآن أصبح لديك نظام صلاحيات مرن للتحكم في من يمكنه الوصول إلى لوحة التحكم. يمكنك إنشاء أدوار مخصصة وإعطاء صلاحيات محددة حسب احتياجاتك.
