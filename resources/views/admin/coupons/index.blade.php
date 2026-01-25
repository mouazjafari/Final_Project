@extends('admin.layouts.admin-layout')
@section('title', 'إدارة الكوبونات - لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coupons-styles.css') }}">
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>🎟️ إدارة الكوبونات</h2>
        <div class="page-stats">
            <span class="stat-badge">الإجمالي: {{ $coupons->count() }}</span>
            <a href="{{ route('admin.coupons.create') }}" class="btn-add">
                ➕ إضافة كوبون جديد
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن كود الكوبون...">
        </div>

        <select class="filter-select" id="typeFilter">
            <option value="">كل الأنواع</option>
            <option value="percentage">نسبة مئوية</option>
            <option value="fixed">رقم ثابت</option>
        </select>

        <select class="filter-select" id="statusFilter">
            <option value="">كل الحالات</option>
            <option value="active">مفعل</option>
            <option value="inactive">معطل</option>
            <option value="expired">منتهي</option>
        </select>
    </div>

    <!-- Coupons Grid -->
    <div class="coupons-grid">
        @foreach ($coupons as $coupon)
            @php
                $isExpired = $coupon->expires_at && $coupon->expires_at->isPast();
                $isActive = $coupon->is_active && !$isExpired;
                $usagePercent = $coupon->max_uses ? ($coupon->used_count / $coupon->max_uses) * 100 : 0;
            @endphp

            <div class="coupon-card {{ $isActive ? 'active' : 'inactive' }}"
                 data-code="{{ strtolower($coupon->code) }}"
                 data-type="{{ $coupon->type }}"
                 data-status="{{ $isActive ? 'active' : ($isExpired ? 'expired' : 'inactive') }}">

                <!-- Coupon Header -->
                <div class="coupon-header">
                    <div class="coupon-code">
                        <span class="code-label">الكود:</span>
                        <span class="code-value">{{ $coupon->code }}</span>
                    </div>

                    <div class="coupon-status">
                        @if ($isExpired)
                            <span class="badge badge-expired">⏰ منتهي</span>
                        @elseif ($isActive)
                            <span class="badge badge-active">✅ مفعل</span>
                        @else
                            <span class="badge badge-inactive">❌ معطل</span>
                        @endif
                    </div>
                </div>

                <!-- Coupon Body -->
                <div class="coupon-body">
                    <!-- Discount Info -->
                    <div class="discount-info">
                        <div class="discount-icon">
                            {{ $coupon->type === 'percentage' ? '📊' : '💵' }}
                        </div>
                        <div class="discount-details">
                            <span class="discount-label">قيمة الخصم:</span>
                            <span class="discount-value">
                                @if ($coupon->type === 'percentage')
                                    {{ $coupon->amount }}%
                                @else
                                    {{ number_format($coupon->amount, 2) }} ₪
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Coupon Details -->
                    <div class="coupon-details">
                        <div class="detail-row">
                            <span class="detail-label">النوع:</span>
                            <span class="detail-value">
                                @if ($coupon->type === 'percentage')
                                    <span class="type-badge type-percentage">📊 نسبة مئوية</span>
                                @else
                                    <span class="type-badge type-fixed">💵 رقم ثابت</span>
                                @endif
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">الاستخدامات:</span>
                            <span class="detail-value">
                                {{ $coupon->used_count }} /
                                {{ $coupon->max_uses ?? '∞' }}
                            </span>
                        </div>

                        @if ($coupon->max_uses)
                            <div class="usage-bar">
                                <div class="usage-progress" style="width: {{ $usagePercent }}%"></div>
                            </div>
                        @endif

                        @if ($coupon->starts_at)
                            <div class="detail-row">
                                <span class="detail-label">يبدأ:</span>
                                <span class="detail-value">{{ $coupon->starts_at->format('Y-m-d') }}</span>
                            </div>
                        @endif

                        @if ($coupon->expires_at)
                            <div class="detail-row">
                                <span class="detail-label">ينتهي:</span>
                                <span class="detail-value">{{ $coupon->expires_at->format('Y-m-d') }}</span>
                            </div>
                        @endif

                        @if ($coupon->allowed_users_count > 0)
                            <div class="detail-row">
                                <span class="detail-label">مستخدمين محددين:</span>
                                <span class="detail-value">{{ $coupon->allowed_users_count }} مستخدم</span>
                            </div>
                        @endif

                        @if ($coupon->description)
                            <div class="detail-row full-width">
                                <span class="detail-label">الوصف:</span>
                                <span class="detail-value description">{{ $coupon->description }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Coupon Actions -->
                <div class="coupon-actions">
                    <button onclick="toggleStatus({{ $coupon->id }})"
                            class="action-btn btn-toggle"
                            title="{{ $coupon->is_active ? 'تعطيل' : 'تفعيل' }}">
                        {{ $coupon->is_active ? '⏸️' : '▶️' }}
                    </button>

                    <a href="{{ route('admin.coupons.usages', $coupon->id) }}"
                       class="action-btn btn-view"
                       title="عرض الاستخدامات">
                        👁️
                    </a>

                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                       class="action-btn btn-edit"
                       title="تعديل">
                        ✏️
                    </a>

                    @if ($coupon->used_count == 0)
                        <button onclick="confirmDelete({{ $coupon->id }}, '{{ $coupon->code }}')"
                                class="action-btn btn-delete"
                                title="حذف">
                            🗑️
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($coupons->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🎟️</div>
            <h3>لا توجد كوبونات حالياً</h3>
            <p>ابدأ بإنشاء أول كوبون خصم!</p>
            <a href="{{ route('admin.coupons.create') }}" class="btn-add">
                ➕ إضافة كوبون جديد
            </a>
        </div>
    @endif

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>⚠️ تأكيد الحذف</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف الكوبون: <strong id="deleteItemName"></strong>؟</p>
                <p class="warning-text">لا يمكن التراجع عن هذا الإجراء!</p>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="submit" class="btn-delete">🗑️ حذف</button>
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/coupons-scripts.js') }}"></script>
@endpush
