@extends('admin.layouts.admin-layout')
@section('title', 'إدارة الأدوار - لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/roles-permissions-styles.css') }}">
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
        <h2>👥 إدارة الأدوار (Roles)</h2>
        <div class="page-stats">
            <span class="stat-badge">الإجمالي: {{ $roles->count() }}</span>
            <a href="{{ route('admin.roles.create') }}" class="btn-add">
                ➕ إضافة دور جديد
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن اسم الدور...">
        </div>

        <select class="filter-select" id="guardFilter">
            <option value="">كل الـ Guards</option>
            <option value="api">API</option>
            <option value="web">WEB</option>
        </select>
    </div>

    <!-- Roles Grid -->
    <div class="roles-grid">
        @foreach ($roles as $role)
            <div class="role-card"
                 data-name="{{ strtolower($role->name) }}"
                 data-guard="{{ $role->guard_name }}">

                <!-- Role Header -->
                <div class="role-header">
                    <div class="role-icon">
                        @if($role->guard_name === 'api')
                            🔌
                        @else
                            🌐
                        @endif
                    </div>
                    <div class="role-info">
                        <div class="role-name">{{ $role->name }}</div>
                        <div class="role-guard">
                            <span class="guard-badge guard-{{ $role->guard_name }}">
                                {{ strtoupper($role->guard_name) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Role Stats -->
                <div class="role-stats">
                    <div class="stat-item">
                        <div class="stat-icon">🔐</div>
                        <div class="stat-details">
                            <div class="stat-label">الصلاحيات</div>
                            <div class="stat-value">{{ $role->permissions_count }}</div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon">👤</div>
                        <div class="stat-details">
                            <div class="stat-label">المستخدمين</div>
                            <div class="stat-value">{{ $role->users_count }}</div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Preview -->
                @if($role->permissions->count() > 0)
                    <div class="permissions-preview">
                        <div class="preview-label">الصلاحيات:</div>
                        <div class="permissions-list">
                            @foreach($role->permissions->take(3) as $permission)
                                <span class="permission-badge">
                                    🔐 {{ $permission->name }}
                                </span>
                            @endforeach
                            @if($role->permissions->count() > 3)
                                <span class="permission-badge more-badge">
                                    +{{ $role->permissions->count() - 3 }} المزيد
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Role Actions -->
                <div class="role-actions">
                    <a href="{{ route('admin.roles.edit', $role->id) }}"
                       class="action-btn btn-edit"
                       title="تعديل">
                        ✏️
                    </a>

                    @if($role->users_count == 0)
                        <button onclick="confirmDelete({{ $role->id }}, '{{ $role->name }}')"
                                class="action-btn btn-delete"
                                title="حذف">
                            🗑️
                        </button>
                    @else
                        <button class="action-btn btn-disabled"
                                title="لا يمكن حذف دور مرتبط بمستخدمين"
                                disabled>
                            🔒
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($roles->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">👥</div>
            <h3>لا توجد أدوار حالياً</h3>
            <p>ابدأ بإنشاء أول دور!</p>
            <a href="{{ route('admin.roles.create') }}" class="btn-add">
                ➕ إضافة دور جديد
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
                <p>هل أنت متأكد من حذف الدور: <strong id="deleteItemName"></strong>؟</p>
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
    <script src="{{ asset('js/roles-permissions-scripts.js') }}"></script>
@endpush
