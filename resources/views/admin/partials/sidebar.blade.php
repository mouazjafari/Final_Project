<aside id="sidebar" class="sidebar" aria-label="{{ __('admin.sidebar_label') }}">
    <div class="sidebar-header">
        <h2>📊 {{ __('admin.main_menu') }}</h2>
    </div>

    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" aria-label="{{ __('admin.toggle_sidebar') }}">☰</button>

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">🏠</span>
                <span class="text">{{ __('admin.dashboard') }}</span>
            </a>
        </li>

        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.users*', 'admin.roles*', 'admin.permissions*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">👥</span>
                <span class="text">{{ __('admin.users_and_roles') }}</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.users*', 'admin.roles*', 'admin.permissions*') ? 'block' : 'none' }}">
                @if(auth()->user()->hasPermissionTo('View all users', 'web'))
                    <li>
                        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <span class="icon">👤</span>
                            <span class="text">{{ __('admin.users') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionTo('view roles', 'web'))
                    <li>
                        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <span class="icon">🎭</span>
                            <span class="text">{{ __('admin.roles') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionTo('view permissions', 'web'))
                    <li>
                        <a href="{{ route('admin.permissions.index') }}" class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <span class="icon">🔐</span>
                            <span class="text">{{ __('admin.permissions') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.designs*', 'admin.design_options*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">🎨</span>
                <span class="text">{{ __('admin.designs') }}</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.designs*', 'admin.design_options*') ? 'block' : 'none' }}">
                @if(auth()->user()->hasPermissionTo('view designs', 'web'))
                    <li>
                        <a href="{{ route('admin.designs') }}" class="{{ request()->routeIs('admin.designs') ? 'active' : '' }}">
                            <span class="icon">👕</span>
                            <span class="text">{{ __('admin.all_designs') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionTo('view design options', 'web'))
                    <li>
                        <a href="{{ route('admin.design_options') }}" class="{{ request()->routeIs('admin.design_options') ? 'active' : '' }}">
                            <span class="icon">⚙️</span>
                            <span class="text">{{ __('admin.design_options') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.orders*', 'admin.wallets*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">🛒</span>
                <span class="text">{{ __('admin.orders_and_payments') }}</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.orders*', 'admin.wallets*') ? 'block' : 'none' }}">
                @if(auth()->user()->hasPermissionTo('view orders', 'web'))
                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <span class="icon">📦</span>
                            <span class="text">{{ __('admin.orders') }}</span>
                        </a>
                    </li>
                @endif
                @if(auth()->user()->hasPermissionTo('add to wallet', 'web'))
                    <li>
                        <a href="{{ route('admin.wallets.index') }}" class="{{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                            <span class="icon">💰</span>
                            <span class="text">{{ __('admin.wallets') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li class="has-dropdown">
            <a href="javascript:void(0)" class="dropdown-toggle {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" onclick="toggleDropdown(this)">
                <span class="icon">🎟️</span>
                <span class="text">{{ __('admin.marketing') }}</span>
                <span class="dropdown-arrow">▼</span>
            </a>
            <ul class="dropdown-menu" style="display: {{ request()->routeIs('admin.coupons*') ? 'block' : 'none' }}">
                @if(auth()->user()->hasPermissionTo('view coupons', 'web'))
                    <li>
                        <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                            <span class="icon">🏷️</span>
                            <span class="text">{{ __('admin.coupons') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li>
            <a href="{{ route('admin.address') }}" class="{{ request()->routeIs('admin.address') ? 'active' : '' }}">
                <span class="icon">📍</span>
                <span class="text">{{ __('admin.addresses') }}</span>
            </a>
        </li>

    </ul>
</aside>
