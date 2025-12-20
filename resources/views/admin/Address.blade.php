@extends('admin.layouts.admin-layout')

@section('title', 'إدارة العناوين - لوحة التحكم')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/addresses-styles.css') }}">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>📍 إدارة عناوين التوصيل</h2>
        <div class="page-stats">
            <span class="stat-badge">إجمالي: {{ $addresses->count() ?? 0 }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            {{-- <span class="search-icon">🔍</span> --}}
            <input type="text" id="searchInput" placeholder="ابحث عن عنوان أو اسم عميل...">
        </div>
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchCityInput" placeholder="ابحث عن المدينة">
        </div>

    </div>

    <!-- Addresses Grid -->
    <div class="addresses-grid">
        @foreach ($addresses as $address)
            <div class="address-card {{ $address->is_default ? 'default' : '' }}">
                <div class="address-header">
                    <div class="address-icon">🏢</div>
                    <div class="address-info">
                        <div class="customer-name">{{ $address->user->name }}</div>
                        <div class="customer-phone">📱 {{ $address->user->phone_number }}</div>
                    </div>
                </div>
                <div class="address-details">
                    <div class="address-row">
                        <span class="address-label">المدينة:</span>
                        <span class="address-value">{{ $address->city->getTranslation('name', 'ar') }}</span>
                    </div>
                    <div class="address-row">
                        <span class="address-label">الحي:</span>
                        <span class="address-value">{{ $address->area }}</span>
                    </div>
                    <div class="address-row">
                        <span class="address-label">الشارع:</span>
                        <span class="address-value">{{ $address->street }}</span>
                    </div>
                    <div class="address-row">
                        <span class="address-label">الطول:</span>
                        <span class="address-value">{{ $address->Longitude ?? '-' }}</span>
                    </div>
                    <div class="address-row">
                        <span class="address-label">العرض:</span>
                        <span class="address-value">{{ $address->Langitude ?? '-' }}</span>
                    </div>
                    <div class="address-row">
                        <span class="address-label">ملاحظات:</span>
                        <span class="address-value">{{ $address->notes ?? '-' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/addresses-scripts.js') }}"></script>
@endpush
