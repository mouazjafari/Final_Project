@extends('admin.layouts.admin-layout')

@section('title', __('admin.dashboard_title'))

@section('content')
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>{{ __('admin.dashboard_summary') }}</h2>
        <div class="page-stats" style="display:flex; gap:12px;">
        </div>
    </div>

    <div class="welcome-card">
        <h2>{{ __('admin.welcome_message') }}</h2>
        <p>{{ __('admin.login_success') }}</p>
    </div>

    <div class="stats-grid">

        <div class="stat-card">
            <div class="icon">📍</div>
            <h3>{{ __('admin.addresses') }}</h3>
            <div class="number">{{ $addressCount ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="icon">👥</div>
            <h3>{{ __('admin.users') }}</h3>
            <div class="number">{{ $usersCount ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="icon">👕</div>
            <h3>{{ __('admin.designs') }}</h3>
            <div class="number">{{ $designCount ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="icon">⚙️</div>
            <h3>{{ __('admin.design_options') }}</h3>
            <div class="number">{{ $designOptionCount ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="icon">📦</div>
            <h3>{{ __('admin.orders') }}</h3>
            <div class="number">{{ $orderCount ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="icon">🏷️</div>
            <h3>{{ __('admin.coupons') }}</h3>
            <div class="number">{{ $couponCount ?? 0 }}</div>
        </div>
        <div class="stat-card">
            <div class="icon">👥</div>
            <h3>{{ __('admin.roles') }}</h3>
            <div class="number">{{ $rolesCount ?? 0 }}</div>
        </div>

        <div class="stat-card">
            <div class="icon">🔐</div>
            <h3>{{ __('admin.permissions') }}</h3>
            <div class="number">{{ $permissionsCount ?? 0 }}</div>
        </div>
    </div>
@endsection
