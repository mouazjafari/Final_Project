// Centralized translations for JavaScript files
const translations = {
    ar: {
        // Common
        confirm: 'تأكيد',
        cancel: 'إلغاء',
        yes: 'نعم',
        no: 'لا',
        save: 'حفظ',
        edit: 'تعديل',
        delete: 'حذف',
        close: 'إغلاق',
        loading: 'جاري التحميل...',
        error: 'خطأ',
        success: 'نجح',

        // Addresses
        deleteAddressConfirm: 'هل أنت متأكد من حذف هذا العنوان؟',
        deleteAddressSuccess: 'تم حذف العنوان بنجاح',
        city: 'المدينة',
        area: 'الحي',
        street: 'الشارع',
        notes: 'ملاحظات',
        customerInfo: 'معلومات العميل',
        deliveryAddress: 'عنوان التوصيل',

        // Orders
        loadingDetails: 'جاري تحميل التفاصيل...',
        orderNumber: 'طلب رقم',
        orderStatus: {
            pending: 'قيد الانتظار',
            processing: 'قيد المعالجة',
            completed: 'اكتمل',
            cancelled: 'ملغي'
        },
        customerName: 'الاسم',
        email: 'البريد',
        orderNotes: 'ملاحظات الطلب',
        designDetails: 'تفاصيل التصاميم',
        undefined: 'غير محدد',

        // Coupons
        changeCouponStatusConfirm: 'هل أنت متأكد من تغيير حالة الكوبون؟',
        updateError: 'حدث خطأ أثناء التحديث',
        connectionError: 'حدث خطأ في الاتصال بالخادم',

        // Wallets
        walletTransactions: 'معاملات المحفظة',
        transaction: 'معاملة',
        amount: 'المبلغ',
        balance: 'الرصيد',

        // Design Options
        option: 'خيار',
        price: 'السعر',
        addNewDesignOption: 'إضافة خيار تصميم جديد',
        editDesignOption: 'تعديل خيار التصميم',
        fillAllFields: 'الرجاء ملء جميع الحقول المطلوبة',
        results: 'النتائج',

        // Roles & Permissions
        role: 'دور',
        permission: 'صلاحية',
        users: 'المستخدمين',
    },
    en: {
        // Common
        confirm: 'Confirm',
        cancel: 'Cancel',
        yes: 'Yes',
        no: 'No',
        save: 'Save',
        edit: 'Edit',
        delete: 'Delete',
        close: 'Close',
        loading: 'Loading...',
        error: 'Error',
        success: 'Success',

        // Addresses
        deleteAddressConfirm: 'Are you sure you want to delete this address?',
        deleteAddressSuccess: 'Address deleted successfully',
        city: 'City',
        area: 'Area',
        street: 'Street',
        notes: 'Notes',
        customerInfo: 'Customer Info',
        deliveryAddress: 'Delivery Address',

        // Orders
        loadingDetails: 'Loading details...',
        orderNumber: 'Order #',
        orderStatus: {
            pending: 'Pending',
            processing: 'Processing',
            completed: 'Completed',
            cancelled: 'Cancelled'
        },
        customerName: 'Name',
        email: 'Email',
        orderNotes: 'Order Notes',
        designDetails: 'Design Details',
        undefined: 'Undefined',

        // Coupons
        changeCouponStatusConfirm: 'Are you sure you want to change the coupon status?',
        updateError: 'An error occurred during update',
        connectionError: 'An error occurred while connecting to the server',

        // Wallets
        walletTransactions: 'Wallet Transactions',
        transaction: 'Transaction',
        amount: 'Amount',
        balance: 'Balance',

        // Design Options
        option: 'Option',
        price: 'Price',
        addNewDesignOption: 'Add New Design Option',
        editDesignOption: 'Edit Design Option',
        fillAllFields: 'Please fill in all required fields',
        results: 'Results',

        // Roles & Permissions
        role: 'Role',
        permission: 'Permission',
        users: 'Users',
    }
};

// Get current language from HTML lang attribute or default to 'ar'
function getCurrentLanguage() {
    return document.documentElement.lang || 'ar';
}

// Get translation by key
function __(key) {
    const lang = getCurrentLanguage();
    const keys = key.split('.');
    let value = translations[lang];

    for (const k of keys) {
        if (value && typeof value === 'object' && k in value) {
            value = value[k];
        } else {
            return key; // Return key if translation not found
        }
    }

    return value || key;
}

// Export for use in other scripts
window.__ = __;
window.translations = translations;
window.getCurrentLanguage = getCurrentLanguage;
