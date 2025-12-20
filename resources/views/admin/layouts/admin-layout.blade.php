<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'إدارة - لوحة التحكم')</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin-styles.css') }}">

    @stack('styles')
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div style="display:flex; align-items:center; gap:12px;">
            <!-- mobile toggle (visible on small screens) -->
            <button id="mobileSidebarToggle" class="sidebar-toggle" aria-label="فتح القائمة">☰</button>
            <h1>🏪 لوحة تحكم المتجر</h1>
        </div>

        <div class="user-info">
            <span>{{ Auth::user()->name ?? 'المدير' }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="logout-btn">🚪 تسجيل الخروج</button>
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
    <script src="{{ asset('js/admin-scripts_dashboard.js') }}"></script>

    @stack('scripts')
</body>

</html>
