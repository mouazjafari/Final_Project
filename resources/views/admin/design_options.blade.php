@extends('admin.layouts.admin-layout')
@section('title', __('admin.design_options_title'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/design-options-styles.css') }}">
@endpush

@section('content')
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

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>{{ __('admin.design_options_management') }}</h2>
        <div class="page-stats">
            <span class="stat-badge">{{ __('admin.total') }}: {{ $designOptions->count() ?? 0 }}</span>
            @if(auth()->user()->hasPermissionTo('create design option', 'web'))
                <button class="add-btn" onclick="openAddModal()">
                    {{ __('admin.add_new_option') }}
                </button>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="{{ __('admin.search_design_option') }}">
        </div>
        <select class="filter-select" id="typeFilter">
            <option value="">{{ __('admin.all_types') }}</option>
            <option value="color">لون (Color)</option>
            <option value="sleeve">كم (Sleeve)</option>
            <option value="dome">قبة (Dome)</option>
            <option value="fabric">قماش (Fabric)</option>
        </select>
    </div>

    <!-- Design Options Grid -->
    <div class="addresses-grid">
        @foreach ($designOptions as $option)
            <div class="address-card" data-type="{{ $option->type }}">
                <div class="address-header">
                    <div class="address-icon">
                        @if ($option->type == 'color')
                            🎨
                        @elseif($option->type == 'sleeve')
                            👔
                        @elseif($option->type == 'dome')
                            🏛️
                        @elseif($option->type == 'fabric')
                            🧵
                        @endif
                    </div>
                    <div class="address-info">
                        <div class="customer-name">{{ $option->getTranslation('name', 'ar') }}</div>
                        <div class="customer-phone">{{ $option->getTranslation('name', 'en') }}</div>
                    </div>
                </div>

                <div class="address-details">
                    <div class="address-row">
                        <span class="address-label">النوع:</span>
                        <span class="address-value type-badge type-{{ $option->type }}">
                            @if ($option->type == 'color')
                                لون
                            @elseif($option->type == 'sleeve')
                                كم
                            @elseif($option->type == 'dome')
                                قبة
                            @elseif($option->type == 'fabric')
                                قماش
                            @endif
                        </span>
                    </div>
                    <div class="address-row">
                        <span class="address-label">تاريخ الإضافة:</span>
                        <span class="address-value">{{ $option->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>

                <div class="card-actions">
                    @if(auth()->user()->hasPermissionTo('update design option', 'web'))
                        <button class="action-btn edit-btn"
                            onclick="openEditModal({{ $option->id }}, '{{ $option->getTranslation('name', 'ar') }}', '{{ $option->getTranslation('name', 'en') }}', '{{ $option->type }}')">
                            ✏️ تعديل
                        </button>
                    @endif
                    @if(auth()->user()->hasPermissionTo('delete design option', 'web'))
                        <button class="action-btn delete-btn"
                            onclick="confirmDelete({{ $option->id }}, '{{ $option->getTranslation('name', 'ar') }}')">
                            🗑️ حذف
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add/Edit Modal -->
    <div id="optionModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">➕ إضافة خيار تصميم جديد</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="optionForm" method="POST">
                @csrf
                <input type="hidden" id="optionId" name="id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <div class="form-group">
                    <label>الاسم بالعربية *</label>
                    <input type="text" name="name_ar" id="nameAr" required placeholder="مثال: أحمر">
                </div>

                <div class="form-group">
                    <label>الاسم بالإنجليزية *</label>
                    <input type="text" name="name_en" id="nameEn" required placeholder="Example: Red">
                </div>

                <div class="form-group">
                    <label>النوع *</label>
                    <select name="type" id="typeSelect" required>
                        <option value="">اختر النوع</option>
                        <option value="color">لون (Color)</option>
                        <option value="sleeve">كم (Sleeve)</option>
                        <option value="dome">قبة (Dome)</option>
                        <option value="fabric">قماش (Fabric)</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn-submit">💾 حفظ</button>
                    <button type="button" class="btn-cancel" onclick="closeModal()">❌ إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>⚠️ تأكيد الحذف</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف خيار التصميم: <strong id="deleteItemName"></strong>؟</p>
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
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/design-options-scripts.js') }}"></script>
@endpush
