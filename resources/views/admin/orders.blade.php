@extends('admin.layouts.admin-layout')
@section('title', 'إدارة الطلبات - لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-orders-styles.css') }}">
@endpush

@section('content')
    {{-- رسائل النجاح والأخطاء --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>🛒 إدارة الطلبات</h2>
        <div class="page-stats">
            <span class="stat-badge">إجمالي الطلبات: {{ $total ?? 0 }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchOrderInput" placeholder="ابحث برقم الطلب...">
        </div>

        <div class="search-box">
            <span class="search-icon">👤</span>
            <input type="text" id="searchUserInput" placeholder="ابحث عن اسم العميل...">
        </div>

        <select class="filter-select" id="statusFilter">
            <option value="">كل الحالات</option>
            <option value="pending">⏳ قيد الانتظار</option>
            <option value="processing">🔄 قيد المعالجة</option>
            <option value="shipped">📦 تم الشحن</option>
            <option value="delivered">✅ تم التوصيل</option>
            <option value="cancelled">❌ ملغي</option>
        </select>

        <select class="filter-select" id="priceFilter">
            <option value="">كل الأسعار</option>
            <option value="0-100">أقل من 100</option>
            <option value="100-300">100 - 300</option>
            <option value="300-500">300 - 500</option>
            <option value="500+">أكثر من 500</option>
        </select>
    </div>

    <!-- Advanced Filters -->
    <div class="advanced-filters">
        <button class="toggle-filters-btn" onclick="toggleAdvancedFilters()">
            <span id="toggleIcon">▼</span> فلاتر متقدمة
        </button>

        <div id="advancedFiltersContent" class="advanced-filters-content" style="display: none;">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>📅 التاريخ من:</label>
                    <input type="date" class="filter-select-small" id="dateFromFilter">
                </div>

                <div class="filter-group">
                    <label>📅 التاريخ إلى:</label>
                    <input type="date" class="filter-select-small" id="dateToFilter">
                </div>
            </div>

            <button class="reset-filters-btn" onclick="resetFilters()">
                🔄 إعادة تعيين الفلاتر
            </button>
        </div>
    </div>

    <!-- Orders Grid -->
    <div class="addresses-grid">
        @foreach ($orders as $order)
            <div class="address-card order-card" data-order-id="{{ $order->id }}"
                data-user-name="{{ strtolower($order->user->name) }}" data-status="{{ $order->status }}"
                data-price="{{ $order->total_price }}" data-date="{{ $order->created_at->format('Y-m-d') }}">

                {{-- <div class="order-image-section">
                    @if ($order->design && $order->design->images && $order->design->images->count() > 0)
                        <img src="{{ asset('storage/' . $order->design->images->first()->path) }}"
                            alt="تصميم الطلب"
                            class="order-design-image">
                    @else
                        <div class="no-image">🎨</div>
                    @endif

                    <div class="order-number-badge">#{{ $order->id }}</div>
                </div> --}}

                <div class="address-header">
                    <div class="address-icon">👤</div>
                    <div class="address-info">
                        <div class="customer-name">{{ $order->user->name }}</div>
                        <div class="customer-phone">📧 {{ $order->user->email }}</div>
                    </div>
                </div>

                <div class="address-details">
                    <span class="address-label">رقم الطلب : {{ $order->id }}</س>
                        <div class="address-row">
                            <span class="address-label">الحالة:</span>
                            <span class="address-value">
                                <select class="status-select status-{{ $order->status }}"
                                    data-order-id="{{ $order->id }}"
                                    onchange="updateOrderStatus({{ $order->id }}, this.value)">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>
                                        ⏳ قيد الانتظار
                                    </option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                        🔄 قيد المعالجة
                                    </option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>
                                        ✅ اكتمل
                                    </option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>
                                        ❌ ملغي
                                    </option>
                                </select>
                            </span>
                        </div>

                        <div class="address-row">
                            <span class="address-label">الإجمالي:</span>
                            <span class="address-value price-value">{{ number_format($order->total_price, 2) }} ₪</span>
                        </div>

                        {{-- @if ($order->design)
                        <div class="address-row">
                            <span class="address-label">التصميم:</span>
                            <span class="address-value">
                                @php
                                    $designName = is_string($order->design->name)
                                        ? json_decode($order->design->name, true)
                                        : $order->design->name;
                                    $displayName = is_array($designName)
                                        ? $designName['ar'] ?? ($designName['en'] ?? 'غير محدد')
                                        : $order->design->name;
                                @endphp
                                {{ $displayName }}
                            </span>
                        </div>
                    @endif --}}

                        @if ($order->size)
                            <div class="address-row">
                                <span class="address-label">المقاس:</span>
                                <span class="address-value">
                                    @php
                                        $sizeName = is_string($order->size->name)
                                            ? json_decode($order->size->name, true)
                                            : $order->size->name;
                                        $displaySizeName = is_array($sizeName)
                                            ? $sizeName['ar'] ?? ($sizeName['en'] ?? 'غير محدد')
                                            : $order->size->name;
                                    @endphp
                                    <span class="size-badge">{{ $displaySizeName }}</span>
                                </span>
                            </div>
                        @endif

                        <div class="address-row">
                            <span class="address-label">التاريخ:</span>
                            <span class="address-value">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                        </div>

                        @if ($order->notes)
                            <div class="address-row">
                                <span class="address-label">ملاحظات:</span>
                                <span class="address-value notes-text">{{ Str::limit($order->notes, 50) }}</span>
                            </div>
                        @endif
                </div>

                <div class="card-actions">
                    <button onclick="showOrderDetails({{ $order->id }})" class="view-btn">
                        👁️ عرض التفاصيل
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>لا توجد طلبات حالياً</h3>
            <p>لم يتم تقديم أي طلبات بعد</p>
        </div>
    @endif

    <!-- Pagination -->
    @if (method_exists($orders, 'links') && $orders->hasPages())
        <div class="pagination-wrapper">
            {{ $orders->links() }}
        </div>
    @endif

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>🛒 تفاصيل الطلب</h3>
                <span class="close" onclick="closeOrderModal()">&times;</span>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <div class="loading-spinner">جاري التحميل...</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin-orders-scripts.js') }}"></script>
@endpush
@push('scripts')
<script>
// ==================== Update Order Status ====================

function updateOrderStatus(orderId, newStatus) {
    console.log('🔄 Updating order:', orderId, 'to status:', newStatus);

    if (!confirm('هل أنت متأكد من تغيير حالة الطلب؟')) {
        location.reload();
        return;
    }

    const selectElement = document.querySelector(`select[data-order-id="${orderId}"]`);

    if (!selectElement) {
        console.error('❌ Select element not found for order:', orderId);
        alert('حدث خطأ: لم يتم العثور على عنصر الاختيار');
        return;
    }

    const originalClass = selectElement.className;

    // إضافة تأثير التحميل
    selectElement.disabled = true;
    selectElement.style.opacity = '0.6';

    // التحقق من وجود CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('❌ CSRF token not found!');
        alert('خطأ: CSRF Token مفقود');
        selectElement.disabled = false;
        selectElement.style.opacity = '1';
        return;
    }

    console.log('📤 Sending request to:', `/admin/orders/${orderId}/status`);

    fetch(`/admin/orders/${orderId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        console.log('📥 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Response data:', data);

        if (data.success) {
            // تحديث class الـ select
            selectElement.className = `status-select status-${newStatus}`;
            selectElement.style.opacity = '1';
            selectElement.disabled = false;

            // تحديث data attribute
            const card = selectElement.closest('.order-card');
            if (card) {
                card.dataset.status = newStatus;

                // تأثير نبض على البطاقة
                card.style.backgroundColor = '#d1fae5';
                setTimeout(() => {
                    card.style.backgroundColor = '';
                }, 1000);
            }

            // إظهار رسالة نجاح
            showSuccessMessage('✅ تم تحديث حالة الطلب بنجاح!');
        } else {
            console.error('❌ Update failed:', data.message);
            showErrorMessage('❌ ' + (data.message || 'حدث خطأ أثناء تحديث الحالة'));
            selectElement.className = originalClass;
            selectElement.disabled = false;
            selectElement.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('❌ Fetch error:', error);
        showErrorMessage('❌ حدث خطأ في الاتصال بالخادم: ' + error.message);
        selectElement.className = originalClass;
        selectElement.disabled = false;
        selectElement.style.opacity = '1';
    });
}

// ==================== Success/Error Messages ====================

function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '99999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '99999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = message;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

console.log('✅ Orders JavaScript loaded successfully!');
</script>
@endpush
