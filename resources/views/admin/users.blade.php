@extends('admin.layouts.admin-layout')

@section('title', 'إدارة المستخدمين - لوحة التحكم')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/users-styles.css') }}">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>👥 إدارة المستخدمين</h2>
        <div class="page-stats">
            <span class="stat-badge">إجمالي: {{ $users->count() ?? 0 }}</span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن اسم، إيميل أو هاتف...">
        </div>

        <select class="filter-select" id="roleFilter">
            <option value="">كل الصلاحيات</option>
            <option value="user">user</option>
            <option value="admin">admin</option>
            <option value="superadmin">superadmin</option>
        </select>

        <select class="filter-select" id="statusFilter">
            <option value="">حالة الحساب</option>
            <option value="active">مفعل</option>
            <option value="inactive">غير مفعل</option>
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
                        <span class="address-label">الهاتف:</span>
                        <span class="address-value">{{ $u->phone_number ?? '-' }}</span>
                    </div>

                    <div class="address-row">
                        <span class="address-label">أنشئ في:</span>
                        <span class="address-value">{{ optional($u->created_at)->format('Y-m-d') ?? '-' }}</span>
                    </div>
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
