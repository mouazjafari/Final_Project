@extends('admin.layouts.admin-layout')
@section('title', 'إدارة المحافظ - لوحة التحكم')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/wallets-styles.css') }}">
@endpush

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <h2>💰 إدارة المحافظ</h2>
        <div class="page-stats">
            <span class="stat-badge">
                المحافظ: {{ $wallets->count() }}
            </span>
            <span class="stat-badge">
                الأرصدة: {{ number_format($wallets->sum('balance'), 2) }} ₪
            </span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-box">
            <span class="search-icon">🔍</span>
            <input type="text" id="searchInput" placeholder="ابحث عن اسم المستخدم...">
        </div>

        <select class="filter-select" id="balanceFilter">
            <option value="">كل الأرصدة</option>
            <option value="zero">رصيد صفر</option>
            <option value="positive">رصيد موجب</option>
            <option value="high">رصيد عالي (500+)</option>
        </select>
    </div>

    <!-- Wallets Grid -->
    <div class="addresses-grid">
        @foreach ($wallets as $wallet)
            <div class="wallet-card"
                 data-user-name="{{ strtolower($wallet->user->name) }}"
                 data-balance="{{ $wallet->balance }}">

                <div class="wallet-header">
                    <div class="wallet-icon">
                        @if ($wallet->balance > 500)
                            💎
                        @elseif ($wallet->balance > 100)
                            💰
                        @else
                            🪙
                        @endif
                    </div>
                    <div class="wallet-info">
                        <div class="user-name">{{ $wallet->user->name }}</div>
                        <div class="user-email">📧 {{ $wallet->user->email }}</div>
                    </div>
                </div>

                <div class="wallet-balance">
                    <div class="balance-label">الرصيد الحالي</div>
                    <div class="balance-amount">{{ number_format($wallet->balance, 2) }} ₪</div>
                </div>

                @if ($wallet->transactions->count() > 0)
                    <div class="recent-transactions">
                        <div class="transactions-label">آخر العمليات:</div>
                        @foreach ($wallet->transactions->take(3) as $trans)
                            <div class="mini-transaction {{ $trans->type }}">
                                <span class="trans-icon">
                                    {{ $trans->type == 'deposit' ? '⬆️' : '⬇️' }}
                                </span>
                                <span class="trans-amount">
                                    {{ $trans->type == 'deposit' ? '+' : '-' }}
                                    {{ number_format($trans->amount, 2) }} ₪
                                </span>
                                <span class="trans-date">
                                    {{ $trans->created_at->diffForHumans() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="card-actions">
                    <a href="{{ route('admin.wallets.show', $wallet->user_id) }}" class="btn-view">
                        👁️ عرض التفاصيل
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    @if ($wallets->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">💸</div>
            <h3>لا توجد محافظ حالياً</h3>
            <p>لم يتم إنشاء أي محفظة بعد</p>
        </div>
    @endif
@endsection

@push('scripts')
<script src="{{ asset('js/wallets-scripts.js') }}"></script>
@endpush
