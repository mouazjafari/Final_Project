@extends('admin.layouts.admin-layout')
@section('title', __('admin.create_new_coupon') . ' - ' . __('admin.dashboard'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/coupons-styles.css') }}">
@endpush

@section('content')
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header" style="background: linear-gradient(90deg,#2c3e50,#34495e);">
        <h2>➕ {{ __('admin.create_new_coupon') }}</h2>
        <a href="{{ route('admin.coupons.index') }}" class="btn-back">
            ← {{ __('admin.back') }}
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.coupons.store') }}" method="POST" id="couponForm">
            @csrf

            <div class="form-grid">
                <!-- كود الكوبون -->
                <div class="form-group full-width">
                    <label for="code">{{ __('admin.coupon_code') }} *</label>
                    <input type="text"
                           name="code"
                           id="code"
                           value="{{ old('code') }}"
                           placeholder="{{ __('admin.coupon_code_placeholder') }}"
                           required
                           maxlength="50">
                    <small>{{ __('admin.coupon_code_hint') }}</small>
                </div>

                <!-- نوع الكوبون -->
                <div class="form-group">
                    <label for="type">{{ __('admin.discount_type') }} *</label>
                    <select name="type" id="type" required onchange="updateAmountLabel()">
                        <option value="">{{ __('admin.select_type') }}</option>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>
                            📊 {{ __('admin.percentage_discount') }}
                        </option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>
                            💵 {{ __('admin.fixed_discount') }}
                        </option>
                    </select>
                </div>

                <!-- قيمة الخصم -->
                <div class="form-group">
                    <label for="amount">
                        <span id="amountLabel">{{ __('admin.discount_amount') }} *</span>
                    </label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           value="{{ old('amount') }}"
                           step="0.01"
                           min="0.01"
                           placeholder="{{ __('admin.amount_placeholder') }}"
                           required>
                    <small id="amountHint">{{ __('admin.enter_value') }}</small>
                </div>

                <!-- الحد الأقصى للاستخدام -->
                <div class="form-group">
                    <label for="max_uses">{{ __('admin.max_uses') }}</label>
                    <input type="number"
                           name="max_uses"
                           id="max_uses"
                           value="{{ old('max_uses') }}"
                           min="1"
                           placeholder="{{ __('admin.leave_empty_unlimited') }}">
                    <small>{{ __('admin.max_uses_hint') }}</small>
                </div>

                <!-- تاريخ البداية -->
                <div class="form-group">
                    <label for="starts_at">{{ __('admin.start_date') }}</label>
                    <input type="datetime-local"
                           name="starts_at"
                           id="starts_at"
                           value="{{ old('starts_at') }}">
                    <small>{{ __('admin.leave_empty_start_now') }}</small>
                </div>

                <!-- تاريخ الانتهاء -->
                <div class="form-group">
                    <label for="expires_at">{{ __('admin.expiry_date') }}</label>
                    <input type="datetime-local"
                           name="expires_at"
                           id="expires_at"
                           value="{{ old('expires_at') }}">
                    <small>{{ __('admin.leave_empty_no_expiry') }}</small>
                </div>

                <!-- الوصف -->
                <div class="form-group full-width">
                    <label for="description">{{ __('admin.description') }}</label>
                    <textarea name="description"
                              id="description"
                              rows="3"
                              placeholder="{{ __('admin.description_optional') }}">{{ old('description') }}</textarea>
                </div>

                <!-- المستخدمين المسموح لهم -->
                <div class="form-group full-width">
                    <label for="allowed_users">{{ __('admin.allowed_users_optional') }}</label>
                    <select name="allowed_users[]"
                            id="allowed_users"
                            multiple
                            class="multi-select">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                    {{ in_array($user->id, old('allowed_users', [])) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    <small>{{ __('admin.select_users_hint') }}</small>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    💾 {{ __('admin.save_coupon') }}
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn-cancel">
                    ❌ {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function updateAmountLabel() {
            const type = document.getElementById('type').value;
            const label = document.getElementById('amountLabel');
            const hint = document.getElementById('amountHint');
            const lang = document.documentElement.lang || 'ar';

            if (type === 'percentage') {
                label.textContent = lang === 'ar' ? 'نسبة الخصم (%) *' : 'Discount Percentage (%) *';
                hint.textContent = lang === 'ar' ? 'مثال: 20 يعني 20%' : 'Example: 20 means 20%';
            } else if (type === 'fixed') {
                label.textContent = lang === 'ar' ? 'مبلغ الخصم (₪) *' : 'Discount Amount (₪) *';
                hint.textContent = lang === 'ar' ? 'مثال: 50 يعني 50 ليرة' : 'Example: 50 means 50 Shekels';
            } else {
                label.textContent = lang === 'ar' ? 'قيمة الخصم *' : 'Discount Value *';
                hint.textContent = lang === 'ar' ? 'اختر نوع الخصم أولاً' : 'Select discount type first';
            }
        }

        // تحويل الكود لأحرف كبيرة تلقائياً
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endpush
