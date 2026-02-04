@extends('admin.layouts.admin-layout')
@section('title', __('admin.designs_title'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/user-designs-styles.css') }}?v={{ time() }}">
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
        <h2>{{ __('admin.designs_management') }}</h2>
        <div class="page-stats">
            <span class="stat-badge">{{ __('admin.total_designs') }}: {{ $designs->count() ?? 0 }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchDesignInput" placeholder="{{ __('admin.search_design_name') }}">
        </div>

        <div class="search-box">
            <span class="search-icon">👤</span>
            <input type="text" id="searchUserInput" placeholder="{{ __('admin.search_user_name') }}">
        </div>

        <select class="filter-select" id="sizeFilter">
            <option value="">{{ __('admin.all_sizes') }}</option>
            <option value="S">S - صغير</option>
            <option value="M">M - وسط</option>
            <option value="L">L - كبير</option>
            <option value="XL">XL - كبير جداً</option>
            <option value="XXL">XXL - كبير جداً جداً</option>
        </select>

        <select class="filter-select" id="priceFilter">
            <option value="">{{ __('admin.all_prices') }}</option>
            <option value="0-50">< 50</option>
            <option value="50-100">50 - 100</option>
            <option value="100-200">100 - 200</option>
            <option value="200-500">200 - 500</option>
            <option value="500+"> > 500</option>
        </select>
    </div>

    <!-- Advanced Filters (Bonus) -->
    <div class="advanced-filters">
        <button class="toggle-filters-btn" onclick="toggleAdvancedFilters()">
            <span id="toggleIcon">▼</span> {{ __('admin.advanced_filters') }}
        </button>

        <div id="advancedFiltersContent" class="advanced-filters-content" style="display: none;">
            <div class="filters-grid">
                <div class="filter-group">
                    <label>{{ __('admin.color') }}:</label>
                    <select class="filter-select-small" id="colorFilter">
                        <option value="">{{ __('admin.all') }}</option>
                        @foreach ($colors ?? [] as $color)
                            <option value="{{ $color->id }}">{{ $color->getTranslation('name', 'ar') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>{{ __('admin.sleeve') }}:</label>
                    <select class="filter-select-small" id="sleeveFilter">
                        <option value="">{{ __('admin.all') }}</option>
                        @foreach ($sleeves ?? [] as $sleeve)
                            <option value="{{ $sleeve->id }}">{{ $sleeve->getTranslation('name', 'ar') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>{{ __('admin.dome') }}:</label>
                    <select class="filter-select-small" id="domeFilter">
                        <option value="">{{ __('admin.all') }}</option>
                        @foreach ($domes ?? [] as $dome)
                            <option value="{{ $dome->id }}">{{ $dome->getTranslation('name', 'ar') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>{{ __('admin.fabric') }}:</label>
                    <select class="filter-select-small" id="fabricFilter">
                        <option value="">{{ __('admin.all') }}</option>
                        @foreach ($fabrics ?? [] as $fabric)
                            <option value="{{ $fabric->id }}">{{ $fabric->getTranslation('name', 'ar') }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button class="reset-filters-btn" onclick="resetFilters()">
                {{ __('admin.reset_filters') }}
            </button>
        </div>
    </div>

    <!-- Designs Grid -->
    <div class="addresses-grid">
        @foreach ($designs as $design)
            @php
                $designName = is_string($design->name) ? json_decode($design->name, true) : $design->name;
                $displayDesignName = is_array($designName)
                    ? $designName['ar'] ?? ($designName['en'] ?? 'غير محدد')
                    : $design->name;
            @endphp

            <div class="address-card design-card" data-design-name="{{ strtolower($displayDesignName) }}"
                data-user-name="{{ strtolower($design->user->name) }}"
                data-size="{{ $design->sizes->pluck('name')->implode(',') }}" data-price="{{ $design->price }}"
                data-color="{{ $design->designOptions->where('type', 'color')->pluck('id')->implode(',') }}"
                data-sleeve="{{ $design->designOptions->where('type', 'sleeve')->pluck('id')->implode(',') }}"
                data-dome="{{ $design->designOptions->where('type', 'dome')->pluck('id')->implode(',') }}"
                data-fabric="{{ $design->designOptions->where('type', 'fabric')->pluck('id')->implode(',') }}">

                <div class="design-image-slider">
                    @if ($design->images && $design->images->count() > 0)
                        <div class="slider-container" data-design-id="{{ $design->id }}">
                            @foreach ($design->images as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                    class="slider-image {{ $index === 0 ? 'active' : '' }}"
                                    alt="{{ $displayDesignName }}">
                            @endforeach

                            @if ($design->images->count() > 1)
                                <button class="slider-prev" onclick="prevImage(this)">‹</button>
                                <button class="slider-next" onclick="nextImage(this)">›</button>
                                <div class="slider-dots">
                                    @foreach ($design->images as $index => $image)
                                        <span class="dot {{ $index === 0 ? 'active' : '' }}"
                                            onclick="goToSlide(this, {{ $index }})"></span>
                                    @endforeach
                                </div>
                                <div class="slider-counter">
                                    <span class="current-slide">1</span> / {{ $design->images->count() }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="no-image">🎨</div>
                    @endif
                </div>

                <div class="address-header">
                    <div class="address-icon">👕</div>
                    <div class="address-info">
                        <div class="customer-name">{{ $displayDesignName }}</div>
                        <div class="customer-phone">👤 {{ $design->user->name }}</div>
                    </div>
                </div>

                <div class="address-details">
                    <div class="address-row">
                        <span class="address-label">{{ __('admin.sizes') }}:</span>
                        <span class="address-value">
                            @if ($design->sizes && $design->sizes->count() > 0)
                                @foreach ($design->sizes as $size)
                                    @php
                                        $sizeName = is_string($size->name)
                                            ? json_decode($size->name, true)
                                            : $size->name;
                                        $displaySizeName = is_array($sizeName)
                                            ? $sizeName['ar'] ?? ($sizeName['en'] ?? 'غير محدد')
                                            : $size->name;
                                    @endphp
                                    <span
                                        class="size-badge size-{{ is_array($sizeName) ? $sizeName['en'] ?? '' : $size->name }}">
                                        {{ $displaySizeName }}
                                    </span>
                                @endforeach
                            @else
                                <span class="size-badge">{{ __('admin.not_available') }}</span>
                            @endif
                        </span>
                    </div>

                    <div class="address-row">
                        <span class="address-label">{{ __('admin.price') }}:</span>
                        <span class="address-value price-value">{{ number_format($design->price, 2) }} ₪</span>
                    </div>

                    {{-- عرض الألوان --}}
                    @if ($design->designOptions->where('type', 'color')->count() > 0)
                        <div class="address-row">
                            <span class="address-label">{{ __('admin.colors') }}:</span>
                            <span class="address-value">
                                @foreach ($design->designOptions->where('type', 'color') as $color)
                                    <span class="option-badge color-badge">
                                        {{ $color->getTranslation('name', 'ar') }}
                                    </span>
                                @endforeach
                            </span>
                        </div>
                    @endif

                    {{-- عرض الأكمام --}}
                    @if ($design->designOptions->where('type', 'sleeve')->count() > 0)
                        <div class="address-row">
                            <span class="address-label">{{ __('admin.sleeves') }}:</span>
                            <span class="address-value">
                                @foreach ($design->designOptions->where('type', 'sleeve') as $sleeve)
                                    <span class="option-badge sleeve-badge">
                                        {{ $sleeve->getTranslation('name', 'ar') }}
                                    </span>
                                @endforeach
                            </span>
                        </div>
                    @endif

                    {{-- عرض القباب --}}
                    @if ($design->designOptions->where('type', 'dome')->count() > 0)
                        <div class="address-row">
                            <span class="address-label">القباب:</span>
                            <span class="address-value">
                                @foreach ($design->designOptions->where('type', 'dome') as $dome)
                                    <span class="option-badge dome-badge">
                                        {{ $dome->getTranslation('name', 'ar') }}
                                    </span>
                                @endforeach
                            </span>
                        </div>
                    @endif

                    {{-- عرض الأقمشة --}}
                    @if ($design->designOptions->where('type', 'fabric')->count() > 0)
                        <div class="address-row">
                            <span class="address-label">الأقمشة:</span>
                            <span class="address-value">
                                @foreach ($design->designOptions->where('type', 'fabric') as $fabric)
                                    <span class="option-badge fabric-badge">
                                        {{ $fabric->getTranslation('name', 'ar') }}
                                    </span>
                                @endforeach
                            </span>
                        </div>
                    @endif

                    <div class="address-row">
                        <span class="address-label">تاريخ الإنشاء:</span>
                        <span class="address-value">{{ $design->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>

                <div class="card-actions">
                    <button onclick="showDesignDetails({{ $design->id }})" class="view-btn">
                        👁️ عرض
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @if ($designs->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>لا توجد تصاميم حالياً</h3>
            <p>لم يقم أي مستخدم بإنشاء تصميم بعد</p>
        </div>
    @endif

    <!-- Design Details Modal -->
    <div id="designDetailsModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>🎨 تفاصيل التصميم</h3>
                <span class="close" onclick="closeDesignModal()">&times;</span>
            </div>
            <div class="modal-body" id="designDetailsContent">
                <div class="loading-spinner">جاري التحميل...</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/user-designs-scripts.js') }}"></script>
@endpush
