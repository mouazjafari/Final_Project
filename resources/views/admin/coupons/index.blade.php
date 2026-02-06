@extends('admin.layouts.admin-layout')
@section('title', __('admin.coupons_title'))

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
        <h2>{{ __('admin.coupons_management') }}</h2>
        <div class="page-stats">
            <span class="stat-badge">{{ __('admin.total') }}: {{ $coupons->count() }}</span>
            @if(auth()->user()->hasPermissionTo('create coupons', 'web'))
                <a href="{{ route('admin.coupons.create') }}" class="btn-add">
                    {{ __('admin.add_new_coupon') }}
                </a>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="{{ __('admin.search_coupon_code') }}">
        </div>

        <select class="filter-select" id="typeFilter">
            <option value="">{{ __('admin.all_types') }}</option>
            <option value="percentage">{{ __('admin.percentage') }}</option>
            <option value="fixed">{{ __('admin.fixed_amount') }}</option>
        </select>

        <select class="filter-select" id="statusFilter">
            <option value="">{{ __('admin.all_statuses') }}</option>
            <option value="active">{{ __('admin.active') }}</option>
            <option value="inactive">{{ __('admin.inactive') }}</option>
            <option value="expired">{{ __('admin.expired') }}</option>
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
                        <span class="code-label">{{ __('admin.code') }}:</span>
                        <span class="code-value">{{ $coupon->code }}</span>
                    </div>

                    <div class="coupon-status">
                        @if ($isExpired)
                            <span class="badge badge-expired">⏰ {{ __('admin.expired') }}</span>
                        @elseif ($isActive)
                            <span class="badge badge-active">✅ {{ __('admin.active') }}</span>
                        @else
                            <span class="badge badge-inactive">❌ {{ __('admin.inactive') }}</span>
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
                            <span class="discount-label">{{ __('admin.discount_value') }}:</span>
                            <span class="discount-value">
                                @if ($coupon->type === 'percentage')
                                    {{ $coupon->amount }}%
                                @else
                                    ${{ number_format($coupon->amount, 2) }}
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Coupon Details -->
                    <div class="coupon-details">
                        <div class="detail-row">
                            <span class="detail-label">{{ __('admin.type') }}:</span>
                            <span class="detail-value">
                                @if ($coupon->type === 'percentage')
                                    <span class="type-badge type-percentage">📊 {{ __('admin.percentage') }}</span>
                                @else
                                    <span class="type-badge type-fixed">💵 {{ __('admin.fixed_amount') }}</span>
                                @endif
                            </span>
                        </div>

                        <div class="detail-row">
                            <span class="detail-label">{{ __('admin.usages') }}:</span>
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
                                <span class="detail-label">{{ __('admin.starts') }}:</span>
                                <span class="detail-value">{{ $coupon->starts_at->format('Y-m-d') }}</span>
                            </div>
                        @endif

                        @if ($coupon->expires_at)
                            <div class="detail-row">
                                <span class="detail-label">{{ __('admin.expires') }}:</span>
                                <span class="detail-value">{{ $coupon->expires_at->format('Y-m-d') }}</span>
                            </div>
                        @endif

                        @if ($coupon->allowed_users_count > 0)
                            <div class="detail-row">
                                <span class="detail-label">{{ __('admin.allowed_users') }}:</span>
                                <span class="detail-value">{{ $coupon->allowed_users_count }} {{ __('admin.users') }}</span>
                            </div>
                        @endif

                        @if ($coupon->description)
                            <div class="detail-row full-width">
                                <span class="detail-label">{{ __('admin.description') }}:</span>
                                <span class="detail-value description">{{ $coupon->description }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Coupon Actions -->
                <div class="coupon-actions">
                    @if(auth()->user()->hasPermissionTo('edit coupons', 'web'))
                        <button onclick="toggleStatus({{ $coupon->id }})"
                                class="action-btn btn-toggle"
                                title="{{ $coupon->is_active ? __('admin.deactivate') : __('admin.activate') }}">
                            {{ $coupon->is_active ? '⏸️' : '▶️' }}
                        </button>

                        <a href="{{ route('admin.coupons.usages', $coupon->id) }}"
                           class="action-btn btn-view"
                           title="{{ __('admin.view_usages') }}">
                            👁️
                        </a>

                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                           class="action-btn btn-edit"
                           title="{{ __('admin.edit') }}">
                            ✏️
                        </a>
                    @endif

                    @if(auth()->user()->hasPermissionTo('delete coupons', 'web'))
                        @if ($coupon->used_count == 0)
                            <button onclick="confirmDelete({{ $coupon->id }}, '{{ $coupon->code }}')"
                                    class="action-btn btn-delete"
                                    title="{{ __('admin.delete') }}">
                                🗑️
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($coupons->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🎟️</div>
            <h3>{{ __('admin.no_coupons') }}</h3>
            <p>{{ __('admin.start_creating_coupon') }}</p>
            <a href="{{ route('admin.coupons.create') }}" class="btn-add">
                ➕ {{ __('admin.add_new_coupon') }}
            </a>
        </div>
    @endif

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>⚠️ {{ __('admin.confirm_delete') }}</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>{{ __('admin.delete_coupon_confirm') }}: <strong id="deleteItemName"></strong>؟</p>
                <p class="warning-text">{{ __('admin.cannot_undo') }}</p>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="submit" class="btn-delete">🗑️ {{ __('admin.delete') }}</button>
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">{{ __('admin.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/coupons-scripts.js') }}"></script>
@endpush
