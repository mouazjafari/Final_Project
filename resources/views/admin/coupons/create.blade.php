@extends('admin.layouts.admin-layout')
@section('title', 'إنشاء كوبون جديد - لوحة التحكم')

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
        <h2>➕ إنشاء كوبون جديد</h2>
        <a href="{{ route('admin.coupons.index') }}" class="btn-back">
            ← العودة
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.coupons.store') }}" method="POST" id="couponForm">
            @csrf

            <div class="form-grid">
                <!-- كود الكوبون -->
                <div class="form-group full-width">
                    <label for="code">كود الكوبون *</label>
                    <input type="text"
                           name="code"
                           id="code"
                           value="{{ old('code') }}"
                           placeholder="مثال: SAVE20"
                           required
                           maxlength="50">
                    <small>يجب أن يكون فريداً (سيتم تحويله لأحرف كبيرة تلقائياً)</small>
                </div>

                <!-- نوع الكوبون -->
                <div class="form-group">
                    <label for="type">نوع الخصم *</label>
                    <select name="type" id="type" required onchange="updateAmountLabel()">
                        <option value="">اختر النوع</option>
                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>
                            📊 نسبة مئوية (%)
                        </option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>
                            💵 رقم ثابت (₪)
                        </option>
                    </select>
                </div>

                <!-- قيمة الخصم -->
                <div class="form-group">
                    <label for="amount">
                        <span id="amountLabel">قيمة الخصم *</span>
                    </label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           value="{{ old('amount') }}"
                           step="0.01"
                           min="0.01"
                           placeholder="مثال: 20"
                           required>
                    <small id="amountHint">أدخل القيمة</small>
                </div>

                <!-- الحد الأقصى للاستخدام -->
                <div class="form-group">
                    <label for="max_uses">الحد الأقصى للاستخدام</label>
                    <input type="number"
                           name="max_uses"
                           id="max_uses"
                           value="{{ old('max_uses') }}"
                           min="1"
                           placeholder="اتركه فارغاً لعدد غير محدود">
                    <small>عدد المرات الكلي المسموح فيها استخدام الكوبون</small>
                </div>

                <!-- تاريخ البداية -->
                <div class="form-group">
                    <label for="starts_at">تاريخ البداية</label>
                    <input type="datetime-local"
                           name="starts_at"
                           id="starts_at"
                           value="{{ old('starts_at') }}">
                    <small>اتركه فارغاً ليبدأ من الآن</small>
                </div>

                <!-- تاريخ الانتهاء -->
                <div class="form-group">
                    <label for="expires_at">تاريخ الانتهاء</label>
                    <input type="datetime-local"
                           name="expires_at"
                           id="expires_at"
                           value="{{ old('expires_at') }}">
                    <small>اتركه فارغاً لعدم وجود تاريخ انتهاء</small>
                </div>

                <!-- الوصف -->
                <div class="form-group full-width">
                    <label for="description">الوصف</label>
                    <textarea name="description"
                              id="description"
                              rows="3"
                              placeholder="وصف اختياري للكوبون...">{{ old('description') }}</textarea>
                </div>

                <!-- المستخدمين المسموح لهم -->
                <div class="form-group full-width">
                    <label for="allowed_users">المستخدمين المسموح لهم (اختياري)</label>
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
                    <small>اضغط Ctrl/Cmd لاختيار أكثر من مستخدم. اتركه فارغاً للسماح لجميع المستخدمين</small>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    💾 حفظ الكوبون
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn-cancel">
                    ❌ إلغاء
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

            if (type === 'percentage') {
                label.textContent = 'نسبة الخصم (%) *';
                hint.textContent = 'مثال: 20 يعني 20%';
            } else if (type === 'fixed') {
                label.textContent = 'مبلغ الخصم (₪) *';
                hint.textContent = 'مثال: 50 يعني 50 ليرة';
            } else {
                label.textContent = 'قيمة الخصم *';
                hint.textContent = 'اختر نوع الخصم أولاً';
            }
        }

        // تحويل الكود لأحرف كبيرة تلقائياً
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endpush
