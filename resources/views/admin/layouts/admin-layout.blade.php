<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', app()->getLocale() == 'ar' ? 'إدارة - لوحة التحكم' : 'Admin - Dashboard')</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}?v={{ time() }}">

    @stack('styles')
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <button id="mobileSidebarToggle" class="sidebar-toggle" aria-label="{{ __('admin.open_menu') }}">☰</button>
            <h1>🏪 {{ __('admin.store_dashboard') }}</h1>
        </div>

        <div class="user-info">
            <!-- Language Switcher -->
            <form action="{{ route('language.switch') }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() == 'ar' ? 'en' : 'ar' }}">
                <button type="submit" class="language-btn" title="{{ app()->getLocale() == 'ar' ? __('admin.switch_to_english') : __('admin.switch_to_arabic') }}">
                    {{ app()->getLocale() == 'ar' ? '🇬🇧 EN' : '🇸🇦 AR' }}
                </button>
            </form>

            <!-- Notifications Button -->
            @php
                $notifications = Auth::user()->unreadNotifications()->latest()->take(10)->get();
                $unreadCount = Auth::user()->unreadNotifications()->count();
            @endphp

            <div class="notifications-wrapper">
                <button class="notifications-btn" id="notificationsBtn" aria-label="{{ __('admin.notifications') }}">
                    🔔
                    @if($unreadCount > 0)
                        <span class="notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </button>

                <!-- Notifications Dropdown -->
                <div class="notifications-dropdown" id="notificationsDropdown">
                    <div class="notifications-header">
                        <h3>🔔 {{ __('admin.notifications') }}</h3>
                        <button class="mark-all-read" onclick="markAllAsRead()">{{ __('admin.mark_all_read') }}</button>
                    </div>
                    <div class="notifications-list">
                        @forelse($notifications as $notification)
                            @php
                                $data = $notification->data;
                                $icon = match($notification->type) {
                                    'App\Notifications\OrderCreatedNotification' => '📦',
                                    'App\Notifications\OrderStatusUpdatedNotification' => '📋',
                                    'App\Notifications\DesignCreatedNotification' => '🎨',
                                    'App\Notifications\PaymentProcessedNotification' => '💰',
                                    'App\Notifications\WalletTransactionNotification' => '💳',
                                    'App\Notifications\CouponCreatedNotification' => '🎟️',
                                    'App\Notifications\DesignOrderStatusNotification' => '👕',
                                    default => '🔔',
                                };
                                $title = $data['title'] ?? __('admin.notifications');
                                $body = $data['message'] ?? $data['body'] ?? __('admin.no_notifications');
                            @endphp

                            <div class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}"
                                 data-notification-id="{{ $notification->id }}"
                                 onclick="markAsRead('{{ $notification->id }}')">
                                <div class="notification-icon">{{ $icon }}</div>
                                <div class="notification-content">
                                    <p class="notification-title">{{ $title }}</p>
                                    <p class="notification-text">{{ $body }}</p>
                                    <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-notifications">
                                <div class="empty-icon">🔕</div>
                                <p>{{ __('admin.no_notifications') }}</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="notifications-footer">
                        <a href="#" class="view-all-notifications">{{ __('admin.view_all_notifications') }}</a>
                    </div>
                </div>
            </div>

            <span>{{ Auth::user()->name ?? __('admin.admin') }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">🚪 {{ __('admin.logout') }}</button>
            </form>
        </div>
    </nav>

    <div class="layout">
        <!-- SIDEBAR -->
        @include('admin.partials.sidebar')

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <div class="container">
                @if (session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="{{ asset('js/translations.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/admin-scripts_dashboard.js') }}?v={{ time() }}"></script>

    @stack('scripts')
</body>

</html>
