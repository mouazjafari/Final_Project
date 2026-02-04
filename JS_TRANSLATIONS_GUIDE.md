# JavaScript Translations System - دليل الاستخدام

## 📖 نظرة عامة

تم إنشاء نظام ترجمة مركزي لملفات JavaScript لدعم تبديل اللغة بين العربية والإنجليزية ديناميكياً.

## 📁 الملفات المشاركة

### 1. ملف الترجمة الرئيسي
```
public/js/translations.js
```
يحتوي على جميع الترجمات للعربية والإنجليزية.

### 2. ملفات JavaScript المحدثة
- `public/js/addresses-scripts.js` - سكريبت صفحة العناوين
- `public/js/admin-orders-scripts.js` - سكريبت صفحة الطلبات
- `public/js/coupons-scripts.js` - سكريبت صفحة الكوبونات
- `public/js/design-options-scripts.js` - سكريبت صفحة خيارات التصميم
- `public/js/user-designs-scripts.js` - سكريبت صفحة تصاميم المستخدمين

### 3. ملفات Blade المحدثة
تم إضافة `translations.js` إلى جميع الصفحات التي تستخدم JavaScript:
- `resources/views/admin/layouts/admin-layout.blade.php` (القالب الرئيسي)
- `resources/views/admin/orders.blade.php`
- `resources/views/admin/coupons/index.blade.php`
- `resources/views/admin/Address.blade.php`
- `resources/views/admin/wallets/index.blade.php`
- `resources/views/admin/wallets/show.blade.php`
- `resources/views/admin/roles/index.blade.php`
- `resources/views/admin/roles/edit.blade.php`
- `resources/views/admin/roles/create.blade.php`
- `resources/views/admin/permissions/index.blade.php`
- `resources/views/admin/design_options.blade.php`
- `resources/views/admin/designs.blade.php`

## 🎯 كيفية الاستخدام

### 1. الحصول على الترجمة

استخدم الدالة `__()` للحصول على الترجمة:

```javascript
// مثال بسيط
alert(__('success'));

// مثال مع نص متداخل
const statusText = __('orderStatus.pending');

// استخدام في رسالة
confirm(__('deleteAddressConfirm'));
```

### 2. الحصول على اللغة الحالية

```javascript
const currentLang = getCurrentLanguage(); // يرجع 'ar' أو 'en'
```

### 3. إضافة ترجمات جديدة

لإضافة ترجمة جديدة، افتح ملف `public/js/translations.js` وأضف المفتاح والقيمة:

```javascript
const translations = {
    ar: {
        myNewKey: 'النص بالعربية',
        nested: {
            key: 'نص متداخل بالعربية'
        }
    },
    en: {
        myNewKey: 'Text in English',
        nested: {
            key: 'Nested text in English'
        }
    }
};
```

ثم استخدمها في الكود:

```javascript
// ترجمة بسيطة
__('myNewKey')

// ترجمة متداخلة
__('nested.key')
```

## 📝 أمثلة عملية

### مثال 1: رسالة تأكيد
```javascript
// القديم
if (confirm('هل أنت متأكد من حذف هذا العنوان؟')) {
    // الكود
}

// الجديد
if (confirm(__('deleteAddressConfirm'))) {
    // الكود
}
```

### مثال 2: تحديث محتوى ديناميكي
```javascript
// القديم
modalTitle.textContent = 'إضافة خيار تصميم جديد';

// الجديد
modalTitle.textContent = __('addNewDesignOption');
```

### مثال 3: التحقق من نص يعتمد على اللغة
```javascript
const cityLabel = getCurrentLanguage() === 'ar' ? 'المدينة' : 'City';

// أو الأفضل، استخدام الترجمة مباشرة
const cityLabel = __('city');
```

## 🔑 المفاتيح المتوفرة

### عام
- `confirm`, `cancel`, `yes`, `no`, `save`, `edit`, `delete`, `close`
- `loading`, `error`, `success`

### العناوين
- `deleteAddressConfirm`, `deleteAddressSuccess`
- `city`, `area`, `street`, `notes`
- `customerInfo`, `deliveryAddress`

### الطلبات
- `loadingDetails`, `orderNumber`
- `orderStatus.pending`, `orderStatus.processing`, `orderStatus.completed`, `orderStatus.cancelled`
- `customerName`, `email`, `orderNotes`, `designDetails`
- `undefined`

### الكوبونات
- `changeCouponStatusConfirm`
- `updateError`, `connectionError`

### خيارات التصميم
- `option`, `price`
- `addNewDesignOption`, `editDesignOption`
- `fillAllFields`, `results`

### الأدوار والصلاحيات
- `role`, `permission`, `users`

## ⚙️ كيف يعمل النظام

1. **تحديد اللغة**: يتم الحصول على اللغة من خاصية `lang` في عنصر `<html>`
2. **البحث عن الترجمة**: تبحث دالة `__()` في كائن الترجمات باستخدام المفتاح المطلوب
3. **التبديل التلقائي**: عند تغيير اللغة من PHP، يتم تحديث جميع النصوص تلقائياً

## 🔄 التزامن مع Laravel

يجب أن تكون اللغة المحددة في Laravel (في session أو config) متطابقة مع خاصية `lang` في HTML:

```blade
<html lang="{{ app()->getLocale() }}">
```

## 💡 نصائح

1. **استخدم دائماً `__()`** بدلاً من النصوص الثابتة
2. **أضف الترجمات الجديدة** في `translations.js` فوراً
3. **استخدم مفاتيح واضحة** تصف المحتوى (مثل: `deleteAddressConfirm` بدلاً من `msg1`)
4. **اختبر اللغتين** عند إضافة نصوص جديدة

## 🐛 استكشاف الأخطاء

### المشكلة: الترجمة لا تعمل
**الحل**: تأكد من:
1. تم تضمين `translations.js` قبل السكريبت الخاص بك
2. المفتاح موجود في كائن الترجمات
3. اللغة محددة بشكل صحيح في `<html lang="...">`

### المشكلة: تظهر المفتاح بدلاً من الترجمة
**الحل**: المفتاح غير موجود في ملف الترجمة، أضفه إلى `translations.js`

## 📞 الدعم

إذا كنت بحاجة إلى إضافة ترجمات جديدة أو لديك أسئلة، راجع الملفات المذكورة أعلاه أو اتصل بفريق التطوير.
