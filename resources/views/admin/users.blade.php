@extends('admin.layouts.admin-layout')

@section('title', __('admin.users_title'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users-styles.css') }}">
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            ✗ {{ session('error') }}
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <h2>{{ __('admin.users_management') }}</h2>
            <div class="page-stats" style="display: flex; align-items: center; gap: 15px;">
                <span class="stat-badge">{{ __('admin.total_users') }}: {{ $users->count() ?? 0 }}</span>
                @if(auth()->user()->hasPermissionTo('create users', 'web'))
                    <a href="{{ route('admin.users.create') }}" class="btn-add-new" style="background: #27ae60; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; display: inline-block; white-space: nowrap;">
                        ➕ إضافة مستخدم جديد
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="{{ __('admin.search_name_email_phone') }}">
        </div>

        <select class="filter-select" id="roleFilter">
            <option value="">{{ __('admin.all_permissions') }}</option>
            <option value="user">user</option>
            <option value="admin">admin</option>
            <option value="superadmin">superadmin</option>
        </select>

        <select class="filter-select" id="statusFilter">
            <option value="">{{ __('admin.account_status') }}</option>
            <option value="active">{{ __('admin.active') }}</option>
            <option value="inactive">{{ __('admin.inactive') }}</option>
        </select>
    </div>

    <!-- Users Grid -->
    <div class="addresses-grid users-grid">
        @foreach ($users as $u)
            <div class="address-card user-card {{ $u->is_active ?? false ? 'default active' : 'inactive' }}"
                data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}"
                data-role="{{ strtolower($u->role ?? '') }}">

                {{-- status badge --}}

                <div class="address-header user-header">
                    <div class="address-icon avatar-wrapper">
                        @if (!empty($u->avatar))
                            <img src="{{ asset('storage/' . $u->avatar) }}" alt="{{ $u->name }}" class="avatar-img">
                        @else
                            <div class="avatar-fallback">{{ strtoupper(substr($u->name ?? '-', 0, 1)) }}</div>
                        @endif
                    </div>

                    <div class="address-info user-info">
                        <div class="customer-name">{{ $u->name ?? '-' }}</div>
                        <div class="customer-phone">📧 {{ $u->email ?? '-' }}</div>
                    </div>
                </div>

                <div class="address-details user-details">
                    <div class="address-row">
                        <span class="address-label">{{ __('admin.phone') }}:</span>
                        <span class="address-value">{{ $u->phone_number ?? '-' }}</span>
                    </div>

                    <div class="address-row">
                        <span class="address-label">الدور:</span>
                        <span class="address-value">
                            @if($u->roles->isNotEmpty())
                                <span style="background: #3498db; color: white; padding: 4px 10px; border-radius: 4px; font-size: 13px;">
                                    {{ $u->roles->first()->name }}
                                </span>
                            @else
                                <span style="color: #999;">بدون دور</span>
                            @endif
                        </span>
                    </div>

                    <div class="address-row">
                        <span class="address-label">{{ __('admin.created_on') }}:</span>
                        <span class="address-value">{{ optional($u->created_at)->format('Y-m-d') ?? '-' }}</span>
                    </div>
                </div>

                <!-- User Actions -->
                <div class="address-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; display: flex; gap: 10px; justify-content: center;">
                    @php
                        $isRegularUser = $u->roles->isNotEmpty() && $u->roles->first()->name === \App\Http\Enum\RoleUserEnum::User->value;
                    @endphp

                    @if(auth()->user()->hasPermissionTo('assign roles to users', 'web') && !$isRegularUser)
                        <a href="{{ route('admin.users.edit', $u->id) }}"
                           class="btn-action btn-edit"
                           style="background: #3498db; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                            ✏️ تعديل الدور
                        </a>
                    @endif

                    @if(auth()->user()->hasPermissionTo('create users', 'web') && $isRegularUser)
                        <form action="{{ route('admin.users.toggleStatus', $u->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="btn-action"
                                    style="background: {{ $u->is_active ? '#27ae60' : '#e74c3c' }}; color: white; padding: 8px 15px; border-radius: 5px; border: none; cursor: pointer; font-size: 14px; display: inline-flex; align-items: center; gap: 5px;">
                                {{ $u->is_active ? '✓ مفعّل' : '✗ معطّل' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if (method_exists($users, 'links'))
        <div class="mt-4 pagination-wrapper">
            {{ $users->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function() {
            const searchInput = document.getElementById('searchInput');
            const roleFilter = document.getElementById('roleFilter');
            const statusFilter = document.getElementById('statusFilter');

            function applyFilters() {
                const q = (searchInput.value || '').trim().toLowerCase();
                const role = roleFilter.value;
                const status = statusFilter.value;

                document.querySelectorAll('.user-card').forEach(card => {
                    const name = (card.dataset.name || '').toLowerCase();
                    const email = (card.dataset.email || '').toLowerCase();
                    const cardRole = (card.dataset.role || '').toLowerCase();
                    const isActive = card.classList.contains('active');

                    let visible = true;

                    if (q && !(name.includes(q) || email.includes(q))) visible = false;
                    if (role && cardRole !== role.toLowerCase()) visible = false;
                    if (status) {
                        if (status === 'active' && !isActive) visible = false;
                        if (status === 'inactive' && isActive) visible = false;
                    }

                    card.style.display = visible ? 'block' : 'none';
                });
            }

            searchInput.addEventListener('input', applyFilters);
            roleFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
        })();
    </script>
@endpush
