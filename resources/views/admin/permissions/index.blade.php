@extends('admin.layouts.admin-layout')
@section('title', __('admin.permissions_title'))

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
        <h2>{{ __('admin.permissions_management') }}</h2>
        <div class="page-stats">
            <span class="stat-badge">{{ __('admin.total') }}: {{ $permissions->count() }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن اسم الصلاحية...">
        </div>

        <select class="filter-select" id="guardFilter">
            <option value="">كل الـ Guards</option>
            <option value="api">API</option>
            <option value="web">WEB</option>
        </select>
    </div>

    <!-- Permissions Table -->
    <div class="permissions-table-card">
        <table class="permissions-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">اسم الصلاحية</th>
                    <th style="width: 15%;">Guard</th>
                    <th style="width: 15%;">عدد الأدوار</th>
                    <th style="width: 15%;">تاريخ الإنشاء</th>
                    <th style="width: 10%;">الإجراءات</th>
                </tr>
            </thead>
            <tbody id="permissionsTableBody">
                @foreach($permissions as $index => $permission)
                    <tr data-name="{{ strtolower($permission->name) }}"
                        data-guard="{{ $permission->guard_name }}">
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="permission-name-badge">
                                🔐 {{ $permission->name }}
                            </span>
                        </td>
                        <td>
                            <span class="guard-badge guard-{{ $permission->guard_name }}">
                                {{ strtoupper($permission->guard_name) }}
                            </span>
                        </td>
                        <td>
                            <span class="count-badge">
                                {{ $permission->roles_count }} أدوار
                            </span>
                        </td>
                        <td>{{ $permission->created_at->format('Y-m-d') }}</td>
                        <td>
                            @if($permission->roles_count == 0)
                                <button onclick="confirmDeletePermission({{ $permission->id }}, '{{ $permission->name }}')"
                                        class="action-btn btn-delete-small"
                                        title="حذف">
                                    🗑️
                                </button>
                            @else
                                <button class="action-btn btn-disabled-small"
                                        title="لا يمكن حذف صلاحية مرتبطة بأدوار"
                                        disabled>
                                    🔒
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($permissions->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">🔐</div>
            <h3>لا توجد صلاحيات حالياً</h3>
            <p>لا توجد صلاحيات لعرضها حالياً.</p>
        </div>
    @endif

    <!-- Delete Permission Modal -->
    <div id="deletePermissionModal" class="modal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h3>⚠️ تأكيد الحذف</h3>
                <span class="close" onclick="closeDeletePermissionModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف الصلاحية: <strong id="deletePermissionName"></strong>؟</p>
                <p class="warning-text">لا يمكن التراجع عن هذا الإجراء!</p>
            </div>
            <form id="deletePermissionForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-actions">
                    <button type="submit" class="btn-delete">🗑️ حذف</button>
                    <button type="button" class="btn-cancel" onclick="closeDeletePermissionModal()">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/roles-permissions-scripts.js') }}"></script>
@endpush
