@extends('admin.layouts.admin-layout')
@section('title', 'تفاصيل المحفظة - ' . $user->name)

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
        <h2>💰 محفظة {{ $user->name }}</h2>
        <a href="{{ route('admin.wallets.index') }}" class="btn-back">
            ← العودة
        </a>
    </div>

    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-card-header">
            <h3>الرصيد الحالي</h3>
        </div>
        <div class="balance-card-body">
            <div class="main-balance">
                {{ number_format($wallet->balance ?? 0, 2) }} ₪
            </div>
            <div class="balance-actions">
                <button class="btn-add" onclick="openAddModal()">
                    ➕ إضافة رصيد
                </button>
                <button class="btn-withdraw" onclick="openWithdrawModal()">
                    ➖ سحب رصيد
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions History -->
    <div class="transactions-section">
        <h3 class="section-title">📜 سجل المعاملات</h3>

        @if ($transactions && $transactions->count() > 0)
            <div class="transactions-table">
                <table>
                    <thead>
                        <tr>
                            <th>التاريخ</th>
                            <th>النوع</th>
                            <th>المبلغ</th>
                            <th>الرصيد قبل</th>
                            <th>الرصيد بعد</th>
                            <th>الأدمن</th>
                            <th>ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $trans)
                            <tr class="trans-row {{ $trans->type }}">
                                <td>{{ $trans->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if ($trans->type == 'deposit')
                                        <span class="badge badge-deposit">⬆️ إيداع</span>
                                    @else
                                        <span class="badge badge-withdraw">⬇️ سحب</span>
                                    @endif
                                </td>
                                <td class="amount-cell {{ $trans->type }}">
                                    {{ $trans->type == 'deposit' ? '+' : '-' }}
                                    {{ number_format($trans->amount, 2) }} ₪
                                </td>
                                <td>{{ number_format($trans->balance_before, 2) }} ₪</td>
                                <td>{{ number_format($trans->balance_after, 2) }} ₪</td>
                                <td>{{ $trans->admin->name ?? 'النظام' }}</td>
                                <td>{{ $trans->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>لا توجد عمليات بعد</p>
            </div>
        @endif
    </div>

    <!-- Add Balance Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➕ إضافة رصيد</h3>
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <form action="{{ route('admin.wallets.add', $user->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           placeholder="مثال: 100.00">
                </div>
                <div class="form-group">
                    <label>ملاحظات (اختياري)</label>
                    <textarea name="notes" rows="3" placeholder="سبب الإضافة..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-submit">💾 إضافة</button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">❌ إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdraw Balance Modal -->
    <div id="withdrawModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➖ سحب رصيد</h3>
                <span class="close" onclick="closeWithdrawModal()">&times;</span>
            </div>
            <form action="{{ route('admin.wallets.withdraw', $user->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>المبلغ *</label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           max="{{ $wallet->balance ?? 0 }}" required
                           placeholder="مثال: 50.00">
                    <small>الحد الأقصى: {{ number_format($wallet->balance ?? 0, 2) }} ₪</small>
                </div>
                <div class="form-group">
                    <label>ملاحظات (اختياري)</label>
                    <textarea name="notes" rows="3" placeholder="سبب السحب..."></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-submit">💾 سحب</button>
                    <button type="button" class="btn-cancel" onclick="closeWithdrawModal()">❌ إلغاء</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/wallets-scripts.js') }}"></script>
@endpush
