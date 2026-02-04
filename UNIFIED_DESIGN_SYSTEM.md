# 🎨 نظام التصميم الموحد - Unified Design System

## نظرة عامة

تم توحيد جميع تصميمات لوحة التحكم (Admin Panel) في ملف CSS واحد موحد لضمان تجربة مستخدم متسقة عبر كافة الصفحات.

## الملفات

### ملف CSS الرئيسي
- **الموقع:** `public/css/unified-admin-styles.css`
- **الاستخدام:** جميع صفحات Admin Panel

### الملفات القديمة (لم تعد مستخدمة)
- ~~`roles-permissions-styles.css`~~
- ~~`users-styles.css`~~
- ~~`design-options-styles.css`~~
- ~~`coupons-styles.css`~~
- ~~`wallets-styles.css`~~
- ~~`admin-orders-styles.css`~~
- ~~`addresses-styles.css`~~
- ~~`user-designs-styles.css`~~

## لوحة الألوان

### الألوان الأساسية
```css
--primary-blue: #3498db     /* الأزرق الأساسي */
--primary-dark: #2c3e50     /* الأزرق الداكن */
--primary-light: #34495e    /* الأزرق الفاتح */
```

### ألوان الحالات
```css
/* النجاح والتفعيل */
--success-green: #27ae60
--success-light: #2ecc71
--success-bg: #d4edda
--success-text: #155724

/* التحذير */
--warning-orange: #f39c12
--warning-light: #f1c40f

/* الخطأ والخطر */
--danger-red: #e74c3c
--danger-light: #c0392b
--error-bg: #f8d7da
--error-text: #721c24

/* المعلومات */
--info-blue: #3498db
--info-light: #5dade2
```

### الألوان المحايدة
```css
--white: #ffffff
--gray-50: #f8f9fa
--gray-100: #f3f4f6
--gray-200: #e5e7eb
--gray-300: #dfe4ea
--gray-400: #cbd5e0
--gray-500: #a0aec0
--gray-600: #718096
--gray-700: #4a5568
--gray-800: #2d3748
--gray-900: #1a202c
```

## المكونات الرئيسية

### 1. Page Header (رأس الصفحة)
```html
<div class="page-header">
    <h2>عنوان الصفحة</h2>
    <div class="page-stats">
        <span class="stat-badge">المجموع: 25</span>
        <a href="#" class="btn-add">إضافة جديد</a>
    </div>
</div>
```

**المميزات:**
- خلفية تدرج لوني أزرق داكن
- محاذاة تلقائية للعناصر
- دعم الأجهزة المحمولة (responsive)

### 2. Filter Section (قسم التصفية)
```html
<div class="filter-section">
    <div class="search-box">
        <span class="search-icon">🔍</span>
        <input type="text" placeholder="ابحث...">
    </div>
    <select class="filter-select">
        <option>خيار 1</option>
    </select>
</div>
```

### 3. Cards (البطاقات)
```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">العنوان</h3>
    </div>
    <!-- المحتوى -->
</div>
```

**أنواع البطاقات:**
- `.card` - بطاقة عامة
- `.role-card` - بطاقة دور
- `.user-card` - بطاقة مستخدم
- `.coupon-card` - بطاقة كوبون
- `.design-option-card` - بطاقة خيار تصميم

**حالات البطاقات:**
- `.active` - نشط (إطار أخضر)
- `.inactive` - غير نشط (إطار رمادي)
- `.expired` - منتهي الصلاحية (إطار أحمر)

### 4. Badges (الشارات)
```html
<span class="badge success">نشط</span>
<span class="badge warning">تحذير</span>
<span class="badge danger">خطر</span>
<span class="badge info">معلومات</span>
<span class="badge neutral">محايد</span>
```

### 5. Buttons (الأزرار)
```html
<button class="btn-add">إضافة</button>
<button class="btn-edit">تعديل</button>
<button class="btn-delete">حذف</button>
<button class="btn-submit">حفظ</button>
<button class="btn-cancel">إلغاء</button>
<button class="btn-back">العودة</button>
```

### 6. Alert Messages (رسائل التنبيه)
```html
<div class="alert alert-success">✓ تمت العملية بنجاح</div>
<div class="alert alert-error">✗ حدث خطأ</div>
<div class="alert alert-warning">⚠ تحذير</div>
<div class="alert alert-info">ℹ معلومات</div>
```

### 7. Forms (النماذج)
```html
<div class="form-card">
    <div class="form-grid">
        <div class="form-group">
            <label>الحقل</label>
            <input type="text">
            <span class="error-text">رسالة خطأ</span>
        </div>
    </div>
    <div class="form-actions">
        <button class="btn-submit">حفظ</button>
        <button class="btn-cancel">إلغاء</button>
    </div>
</div>
```

### 8. Tables (الجداول)
```html
<div class="table-card">
    <table class="table">
        <thead>
            <tr>
                <th>العمود 1</th>
                <th>العمود 2</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>بيانات</td>
                <td>بيانات</td>
            </tr>
        </tbody>
    </table>
</div>
```

### 9. Grid Layouts (شبكات العرض)
```html
<div class="users-grid">
    <!-- البطاقات هنا -->
</div>
```

**الشبكات المتاحة:**
- `.roles-grid`
- `.users-grid`
- `.addresses-grid`
- `.coupons-grid`
- `.design-options-grid`

## الظلال والحواف

### الظلال
```css
--shadow-sm: 0 2px 10px rgba(0,0,0,0.05)    /* ظل صغير */
--shadow-md: 0 4px 15px rgba(0,0,0,0.1)     /* ظل متوسط */
--shadow-lg: 0 6px 24px rgba(0,0,0,0.12)    /* ظل كبير */
--shadow-hover: 0 8px 30px rgba(0,0,0,0.15) /* ظل عند التمرير */
```

### نصف قطر الحواف
```css
--radius-sm: 6px     /* صغير */
--radius-md: 8px     /* متوسط */
--radius-lg: 12px    /* كبير */
--radius-xl: 16px    /* كبير جداً */
--radius-full: 999px /* دائري كامل */
```

## المسافات

```css
--space-xs: 0.5rem   /* 8px */
--space-sm: 0.75rem  /* 12px */
--space-md: 1rem     /* 16px */
--space-lg: 1.5rem   /* 24px */
--space-xl: 2rem     /* 32px */
```

## التحولات والانتقالات

```css
--transition-fast: all 0.2s ease     /* سريع */
--transition-normal: all 0.3s ease   /* عادي */
--transition-slow: all 0.4s ease     /* بطيء */
```

## Utility Classes (فئات مساعدة)

### المحاذاة
```css
.text-center   /* محاذاة للوسط */
.text-right    /* محاذاة لليمين */
.text-left     /* محاذاة لليسار */
```

### المسافات
```css
.mt-sm, .mt-md, .mt-lg, .mt-xl  /* Margin Top */
.mb-sm, .mb-md, .mb-lg, .mb-xl  /* Margin Bottom */
.p-sm, .p-md, .p-lg, .p-xl      /* Padding */
```

### الألوان
```css
/* ألوان النص */
.text-primary   /* أزرق */
.text-success   /* أخضر */
.text-danger    /* أحمر */
.text-warning   /* برتقالي */
.text-gray      /* رمادي */

/* ألوان الخلفية */
.bg-primary
.bg-success
.bg-danger
.bg-warning
.bg-gray
```

## التصميم المتجاوب (Responsive)

جميع المكونات متجاوبة تلقائياً مع الأجهزة المختلفة:

- **Desktop:** 3-4 أعمدة في الشبكة
- **Tablet (< 768px):** عمود واحد
- **Mobile:** تصميم رأسي كامل

## الصفحات المحدثة

تم تطبيق النظام الموحد على الصفحات التالية:

✅ Roles (الأدوار)
- index.blade.php
- create.blade.php
- edit.blade.php

✅ Permissions (الصلاحيات)
- index.blade.php

✅ Users (المستخدمين)
- users.blade.php
- create.blade.php
- edit.blade.php

✅ Design Options (خيارات التصاميم)
- design_options.blade.php

✅ Coupons (الكوبونات)
- index.blade.php
- create.blade.php
- edit.blade.php
- usages.blade.php

✅ Orders (الطلبات)
- orders.blade.php

✅ Wallets (المحافظ)
- index.blade.php
- show.blade.php

✅ Designs (التصاميم)
- designs.blade.php

✅ Addresses (العناوين)
- Address.blade.php

## أمثلة الاستخدام

### مثال 1: صفحة قائمة (Index Page)
```blade
@extends('admin.layouts.admin-layout')
@section('title', 'العنوان')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/unified-admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-header">
        <h2>📋 العنوان</h2>
        <div class="page-stats">
            <span class="stat-badge">المجموع: {{ $items->count() }}</span>
            <a href="{{ route('items.create') }}" class="btn-add">إضافة</a>
        </div>
    </div>

    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث...">
        </div>
    </div>

    <div class="items-grid">
        @foreach($items as $item)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $item->name }}</h3>
                </div>
                <!-- المحتوى -->
            </div>
        @endforeach
    </div>
@endsection
```

### مثال 2: صفحة نموذج (Form Page)
```blade
@extends('admin.layouts.admin-layout')
@section('title', 'إنشاء جديد')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/unified-admin-styles.css') }}">
@endpush

@section('content')
    <div class="page-header">
        <h2>➕ إنشاء جديد</h2>
        <a href="{{ route('items.index') }}" class="btn-back">← العودة</a>
    </div>

    <div class="form-card">
        <form action="{{ route('items.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>الاسم *</label>
                    <input type="text" name="name" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">✓ حفظ</button>
                <a href="{{ route('items.index') }}" class="btn-cancel">✗ إلغاء</a>
            </div>
        </form>
    </div>
@endsection
```

## ملاحظات مهمة

1. **استخدام CSS Variables:** جميع الألوان والمسافات مخزنة في متغيرات CSS يمكن تعديلها بسهولة
2. **التجاوب التلقائي:** جميع المكونات متجاوبة تلقائياً
3. **التسلسل الهرمي:** يمكن استخدام المكونات داخل بعضها بسهولة
4. **الأيقونات:** استخدام Emoji للأيقونات (لا حاجة لمكتبات خارجية)
5. **RTL Support:** دعم كامل للغة العربية (من اليمين لليسار)

## الصيانة والتطوير

### إضافة لون جديد
```css
:root {
    --new-color: #hexcode;
}
```

### إضافة مكون جديد
1. استخدم متغيرات CSS الموجودة
2. اتبع نفس نمط التسمية
3. أضف Hover و Focus states
4. تأكد من التجاوب

### التحديثات المستقبلية
- يمكن إضافة Dark Mode بسهولة باستخدام CSS Variables
- يمكن تخصيص الألوان لكل مستأجر (Multi-tenancy)
- دعم المزيد من المتغيرات اللغوية

## الدعم والمساعدة

للمزيد من المعلومات أو المساعدة:
- راجع ملف `unified-admin-styles.css`
- تحقق من الأمثلة في صفحات Admin الموجودة
- استخدم Developer Tools للفحص والتعديل

---

**آخر تحديث:** فبراير 2026
**الإصدار:** 1.0
