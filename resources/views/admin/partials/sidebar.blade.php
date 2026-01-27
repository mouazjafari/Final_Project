<!-- resources/views/admin/partials/sidebar.blade.php -->
<aside id="sidebar" class="sidebar" aria-label="القائمة الجانبية">
    <div class="sidebar-header">
        <h2>📊 القائمة الرئيسية</h2>
    </div>

    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="طي/فتح الشريط">☰</button>

    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">🏠</span>
                <span class="text">لوحة التحكم</span>
            </a>
        </li>

        <!-- ==================== Users & Roles ==================== -->
        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.users*', 'admin.roles*', 'admin.permissions*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">👥</span>
                <span class="text">المستخدمين والصلاحيات</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.users*', 'admin.roles*', 'admin.permissions*') ? 'block' : 'none' }}">
                <li>
                    <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <span class="icon">👤</span>
                        <span class="text">المستخدمين</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <span class="icon">🎭</span>
                        <span class="text">الأدوار</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.permissions.index') }}" class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                        <span class="icon">🔐</span>
                        <span class="text">الصلاحيات</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ==================== Designs ==================== -->
        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.designs*', 'admin.design_options*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">🎨</span>
                <span class="text">التصميمات</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.designs*', 'admin.design_options*') ? 'block' : 'none' }}">
                <li>
                    <a href="{{ route('admin.designs') }}" class="{{ request()->routeIs('admin.designs') ? 'active' : '' }}">
                        <span class="icon">👕</span>
                        <span class="text">كل التصميمات</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.design_options') }}" class="{{ request()->routeIs('admin.design_options') ? 'active' : '' }}">
                        <span class="icon">⚙️</span>
                        <span class="text">خيارات التصميم</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ==================== Orders & Payments ==================== -->
        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.orders*', 'admin.wallets*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">🛒</span>
                <span class="text">الطلبات والمدفوعات</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.orders*', 'admin.wallets*') ? 'block' : 'none' }}">
                <li>
                    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <span class="icon">📦</span>
                        <span class="text">الطلبات</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.wallets.index') }}" class="{{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                        <span class="icon">💰</span>
                        <span class="text">المحافظ</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ==================== Marketing ==================== -->
        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">🎟️</span>
                <span class="text">التسويق</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.coupons*') ? 'block' : 'none' }}">
                <li>
                    <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                        <span class="icon">🏷️</span>
                        <span class="text">الكوبونات</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- ==================== Locations ==================== -->
        <li>
            <a href="{{ route('admin.address') }}" class="{{ request()->routeIs('admin.address') ? 'active' : '' }}">
                <span class="icon">📍</span>
                <span class="text">العناوين</span>
            </a>
        </li>

    </ul>
</aside>
