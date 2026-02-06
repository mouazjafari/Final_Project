# Database ERD - Kandura E-Commerce

## Entity Relationship Diagram

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        string phone_number UK
        string password
        text Profile_image
        boolean is_active
        timestamp email_verified_at
        string fcm_token
        timestamps timestamps
    }

    cities {
        bigint id PK
        json name
        timestamps timestamps
    }

    addresses {
        bigint id PK
        bigint user_id FK
        bigint city_id FK
        string area
        string street
        decimal Langitude
        decimal Longitude
        text notes
        timestamps timestamps
    }

    designs {
        bigint id PK
        bigint user_id FK
        json name
        json description
        double price
        integer quantity
        timestamps timestamps
    }

    images {
        bigint id PK
        bigint design_id FK
        string image_path
        timestamps timestamps
    }

    sizes {
        bigint id PK
        string name
        timestamps timestamps
    }

    design_size {
        bigint id PK
        bigint design_id FK
        bigint size_id FK
        timestamps timestamps
    }

    design_options {
        bigint id PK
        json name
        string type
        timestamps timestamps
    }

    design_design_options {
        bigint id PK
        bigint design_id FK
        bigint design_options_id FK
        timestamps timestamps
    }

    orders {
        bigint id PK
        bigint user_id FK
        bigint address_id FK
        bigint coupon_id FK
        enum status
        decimal total_price
        text notes
        timestamps timestamps
    }

    design_order {
        bigint id PK
        bigint design_id FK
        bigint order_id FK
        bigint size_id FK
        integer quantity
        decimal unit_price
        timestamps timestamps
    }

    design_option_selected {
        bigint id PK
        bigint design_order_id FK
        bigint design_option_id FK
        timestamps timestamps
    }

    coupons {
        bigint id PK
        string code UK
        enum type
        decimal amount
        integer max_uses
        integer used_count
        timestamp starts_at
        timestamp expires_at
        boolean is_active
        text description
        timestamps timestamps
    }

    coupon_usages {
        bigint id PK
        bigint coupon_id FK
        bigint user_id FK
        bigint order_id FK
        timestamps timestamps
    }

    payments {
        bigint id PK
        bigint order_id FK
        bigint user_id FK
        string payment_method
        string stripe_payment_intent_id
        string stripe_session_id
        string stripe_charge_id
        decimal amount
        enum status
        string currency
        text failure_reason
        text metadata
        timestamp paid_at
        timestamps timestamps
    }

    wallets {
        bigint id PK
        bigint user_id FK,UK
        decimal balance
        timestamps timestamps
    }

    wallet_transactions {
        bigint id PK
        bigint wallet_id FK
        bigint admin_id FK
        enum type
        decimal amount
        decimal balance_before
        decimal balance_after
        text notes
        timestamps timestamps
    }

    invoices {
        bigint id PK
        string invoice_number UK
        bigint order_id FK
        decimal total
        string pdf_url
        timestamps timestamps
    }

    reviews {
        bigint id PK
        bigint order_id FK
        bigint user_id FK
        text comment
        enum rating
        timestamps timestamps
    }

    notifications {
        uuid id PK
        string type
        string notifiable_type
        bigint notifiable_id
        text data
        timestamp read_at
        timestamps timestamps
    }

    users ||--o{ addresses : "has many"
    users ||--o{ designs : "creates"
    users ||--o{ orders : "places"
    users ||--o{ payments : "makes"
    users ||--|| wallets : "has one"
    users ||--o{ reviews : "writes"
    users ||--o{ notifications : "receives"

    cities ||--o{ addresses : "has many"

    designs ||--o{ images : "has many"
    designs ||--o{ design_size : "has many"
    designs ||--o{ design_design_options : "has many"
    designs ||--o{ design_order : "ordered in"

    sizes ||--o{ design_size : "has many"
    sizes ||--o{ design_order : "used in"

    design_options ||--o{ design_design_options : "has many"
    design_options ||--o{ design_option_selected : "selected in"

    addresses ||--o{ orders : "used for"

    orders ||--o{ design_order : "contains"
    orders ||--o{ payments : "has"
    orders ||--|| invoices : "has one"
    orders ||--o{ reviews : "reviewed by"
    orders ||--o{ coupon_usages : "uses"

    coupons ||--o{ coupon_usages : "used in"
    coupons ||--o{ orders : "applied to"

    design_order ||--o{ design_option_selected : "has options"

    wallets ||--o{ wallet_transactions : "has"
```

---

## الجداول | Tables Summary

### جداول المستخدمين | User Tables

| الجدول | الوصف |
|--------|-------|
| `users` | المستخدمين (عملاء + أدمن) |
| `addresses` | عناوين المستخدمين |
| `cities` | المدن |
| `wallets` | محافظ المستخدمين |
| `wallet_transactions` | معاملات المحفظة (إيداع/سحب) |

### جداول التصاميم | Design Tables

| الجدول | الوصف |
|--------|-------|
| `designs` | التصاميم المخصصة |
| `images` | صور التصاميم |
| `sizes` | المقاسات المتاحة |
| `design_size` | علاقة M:N بين التصاميم والمقاسات |
| `design_options` | خيارات التصميم (لون، نوع قماش، الخ) |
| `design_design_options` | علاقة M:N بين التصاميم وخياراتها |

### جداول الطلبات | Order Tables

| الجدول | الوصف |
|--------|-------|
| `orders` | الطلبات الرئيسية |
| `design_order` | تفاصيل الطلب (التصاميم المطلوبة) |
| `design_option_selected` | الخيارات المختارة لكل تصميم في الطلب |
| `payments` | معاملات الدفع (Stripe) |
| `invoices` | الفواتير |
| `reviews` | تقييمات الطلبات |

### جداول الكوبونات | Coupon Tables

| الجدول | الوصف |
|--------|-------|
| `coupons` | كوبونات الخصم |
| `coupon_usages` | سجل استخدام الكوبونات |

### جداول النظام | System Tables

| الجدول | الوصف |
|--------|-------|
| `notifications` | إشعارات المستخدمين |
| `sessions` | جلسات تسجيل الدخول |
| `jobs` | Queue jobs |
| `cache` | Cache storage |

---

## العلاقات الرئيسية | Key Relationships

### design_option_selected

هذا الجدول يربط بين:
- `design_order` (التصميم المطلوب في الطلب)
- `design_options` (الخيار المحدد مثل اللون أو نوع القماش)

```
Order → design_order → design_option_selected ← design_options
        (التصميم+المقاس)     (الخيارات المختارة)    (خيارات التصميم)
```

**مثال:**
- طلب #5 يحتوي Design #3 بمقاس Large
- العميل اختار: لون أبيض + قماش قطني + تطريز ذهبي
- كل خيار يُحفظ في `design_option_selected`

---

## Migration Files

| الملف | الجدول |
|-------|--------|
| `0001_01_01_000000_create_users_table.php` | users, sessions |
| `2025_11_19_190915_create_cities_table.php` | cities |
| `2025_11_19_200052_create_addresses_table.php` | addresses |
| `2025_11_28_180257_create_designs_table.php` | designs |
| `2025_11_28_181418_create_design_options_table.php` | design_options |
| `2025_11_28_181607_create_design_design_options_table.php` | design_design_options |
| `2025_11_28_182107_create_images_table.php` | images |
| `2025_11_29_121101_create_sizes_table.php` | sizes |
| `2025_11_29_121149_create_design_size_table.php` | design_size |
| `2025_12_11_114424_create_orders_table.php` | orders |
| `2025_12_11_115637_create_design_order_table.php` | design_order |
| `2025_12_11_115807_create_design_option_selected_table.php` | design_option_selected |
| `2025_12_23_150047_create_wallets_table.php` | wallets, wallet_transactions |
| `2026_01_19_192033_create_payments_table.php` | payments |
| `2026_01_25_042532_create_coupons_table.php` | coupons |
| `2026_01_25_042757_create_coupon_usages_table.php` | coupon_usages |
| `2026_01_26_212751_create_invoices_table.php` | invoices |
| `2026_01_27_094734_create_reviews_table.php` | reviews |
| `2026_01_28_204724_create_notification_table.php` | notifications |
| `2026_02_04_000001_add_fcm_token_to_users_table.php` | (adds fcm_token to users) |
