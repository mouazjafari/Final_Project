@extends('admin.layouts.admin-layout')
@section('title', __('admin.wallet_details') . ' - ' . $user->name)

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
        <h2>💰 {{ __('admin.wallet_details') }} - {{ $user->name }}</h2>
        <a href="{{ route('admin.wallets.index') }}" class="btn-back">
            ← {{ __('admin.back') }}
        </a>
    </div>

    <!-- Balance Card -->
    <div class="balance-card">
        <div class="balance-card-header">
            <h3>{{ __('admin.current_balance') }}</h3>
        </div>
        <div class="balance-card-body">
            <div class="main-balance">
                ${{ number_format($wallet->balance ?? 0, 2) }}
            </div>
            <div class="balance-actions">
                <button class="btn-add" onclick="openAddModal()">
                    ➕ {{ __('admin.add_balance') }}
                </button>
                <button class="btn-withdraw" onclick="openWithdrawModal()">
                    ➖ {{ __('admin.withdraw_balance') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions History -->
    <div class="transactions-section">
        <h3 class="section-title">📜 {{ __('admin.transactions_log') }}</h3>

        @if ($transactions && $transactions->count() > 0)
            <div class="transactions-table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('admin.transaction_date') }}</th>
                            <th>{{ __('admin.transaction_type') }}</th>
                            <th>{{ __('admin.amount') }}</th>
                            <th>{{ __('admin.balance_before') }}</th>
                            <th>{{ __('admin.balance_after') }}</th>
                            <th>{{ __('admin.admin_name') }}</th>
                            <th>{{ __('admin.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $trans)
                            <tr class="trans-row {{ $trans->type }}">
                                <td>{{ $trans->created_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if ($trans->type == 'deposit')
                                        <span class="badge badge-deposit">⬆️ {{ __('admin.deposit') }}</span>
                                    @else
                                        <span class="badge badge-withdraw">⬇️ {{ __('admin.withdraw') }}</span>
                                    @endif
                                </td>
                                <td class="amount-cell {{ $trans->type }}">
                                    {{ $trans->type == 'deposit' ? '+' : '-' }}
                                    ${{ number_format($trans->amount, 2) }}
                                </td>
                                <td>${{ number_format($trans->balance_before, 2) }}</td>
                                <td>${{ number_format($trans->balance_after, 2) }}</td>
                                <td>{{ $trans->admin->name ?? __('admin.system') }}</td>
                                <td>{{ $trans->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <p>{{ __('admin.no_transactions') }}</p>
            </div>
        @endif
    </div>

    <!-- Add Balance Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➕ {{ __('admin.add_balance') }}</h3>
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <form action="{{ route('admin.wallets.add', $user->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>{{ __('admin.amount_required') }}</label>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                           placeholder="{{ __('admin.deposit_reason') }}">
                </div>
                <div class="form-group">
                    <label>{{ __('admin.notes_optional') }}</label>
                    <textarea name="notes" rows="3" placeholder="{{ __('admin.deposit_reason') }}"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-submit">💾 {{ __('admin.add') }}</button>
                    <button type="button" class="btn-cancel" onclick="closeAddModal()">❌ {{ __('admin.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdraw Balance Modal -->
    <div id="withdrawModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>➖ {{ __('admin.withdraw_balance') }}</h3>
                <span class="close" onclick="closeWithdrawModal()">&times;</span>
            </div>
            <form action="{{ route('admin.wallets.withdraw', $user->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>{{ __('admin.amount_required') }}</label>
                    <input type="number" name="amount" step="0.01" min="0.01"
                           max="{{ $wallet->balance ?? 0 }}" required
                           placeholder="{{ __('admin.withdraw_reason') }}">
                    <small>{{ __('admin.maximum') }}: ${{ number_format($wallet->balance ?? 0, 2) }}</small>
                </div>
                <div class="form-group">
                    <label>{{ __('admin.notes_optional') }}</label>
                    <textarea name="notes" rows="3" placeholder="{{ __('admin.withdraw_reason') }}"></textarea>
                </div>
                <div class="modal-actions">
                    <button type="submit" class="btn-submit">💾 {{ __('admin.withdraw') }}</button>
                    <button type="button" class="btn-cancel" onclick="closeWithdrawModal()">❌ {{ __('admin.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/translations.js') }}"></script>
<script src="{{ asset('js/wallets-scripts.js') }}"></script>
@endpush
