@extends('admin.layouts.admin-layout')
@section('title', 'تعديل الكوبون - لوحة التحكم')

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
        <h2>✏️ تعديل الكوبون: {{ $coupon->code }}</h2>
        <a href="{{ route('admin.coupons.index') }}" class="btn-back">
            ← العودة
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" id="couponForm">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- كود الكوبون -->
                <div class="form-group full-width">
                    <label for="code">كود الكوبون *</label>
                    <input type="text"
                           name="code"
                           id="code"
                           value="{{ old('code', $coupon->code) }}"
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
                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>
                            📊 نسبة مئوية (%)
                        </option>
                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>
                            💵 رقم ثابت (₪)
                        </option>
                    </select>
                </div>

                <!-- قيمة الخصم -->
                <div class="form-group">
                    <label for="amount">
                        <span id="amountLabel">
                            @if($coupon->type === 'percentage')
                                نسبة الخصم (%) *
                            @else
                                مبلغ الخصم (₪) *
                            @endif
                        </span>
                    </label>
                    <input type="number"
                           name="amount"
                           id="amount"
                           value="{{ old('amount', $coupon->amount) }}"
                           step="0.01"
                           min="0.01"
                           placeholder="مثال: 20"
                           required>
                    <small id="amountHint">
                        @if($coupon->type === 'percentage')
                            مثال: 20 يعني 20%
                        @else
                            مثال: 50 يعني 50 ليرة
                        @endif
                    </small>
                </div>

                <!-- الحد الأقصى للاستخدام -->
                <div class="form-group">
                    <label for="max_uses">الحد الأقصى للاستخدام</label>
                    <input type="number"
                           name="max_uses"
                           id="max_uses"
                           value="{{ old('max_uses', $coupon->max_uses) }}"
                           min="1"
                           placeholder="اتركه فارغاً لعدد غير محدود">
                    <small>عدد المرات الكلي المسموح فيها استخدام الكوبون</small>
                </div>

                <!-- عداد الاستخدام الحالي (للعرض فقط) -->
                <div class="form-group">
                    <label>عدد الاستخدامات الحالية</label>
                    <input type="text"
                           value="{{ $coupon->used_count }}"
                           disabled
                           style="background: #f3f4f6; cursor: not-allowed;">
                    <small>لا يمكن تعديل هذا العدد</small>
                </div>

                <!-- تاريخ البداية -->
                <div class="form-group">
                    <label for="starts_at">تاريخ البداية</label>
                    <input type="datetime-local"
                           name="starts_at"
                           id="starts_at"
                           value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                    <small>اتركه فارغاً ليبدأ من الآن</small>
                </div>

                <!-- تاريخ الانتهاء -->
                <div class="form-group">
                    <label for="expires_at">تاريخ الانتهاء</label>
                    <input type="datetime-local"
                           name="expires_at"
                           id="expires_at"
                           value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                    <small>اتركه فارغاً لعدم وجود تاريخ انتهاء</small>
                </div>

                <!-- حالة التفعيل -->
                <div class="form-group full-width">
                    <label for="is_active" style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               value="1"
                               {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                               style="width: 20px; height: 20px; cursor: pointer;">
                        <span style="font-size: 16px;">✅ الكوبون مفعل</span>
                    </label>
                    <small>إذا تم إلغاء التفعيل، لن يتمكن أي مستخدم من استخدام الكوبون</small>
                </div>

                <!-- الوصف -->
                <div class="form-group full-width">
                    <label for="description">الوصف</label>
                    <textarea name="description"
                              id="description"
                              rows="3"
                              placeholder="وصف اختياري للكوبون...">{{ old('description', $coupon->description) }}</textarea>
                </div>

                <!-- المستخدمين المسموح لهم -->
                <div class="form-group full-width">
                    <label for="allowed_users">المستخدمين المسموح لهم (اختياري)</label>
                    <select name="allowed_users[]"
                            id="allowed_users"
                            multiple
                            class="multi-select">
                        @php
                            $selectedUsers = old('allowed_users', $coupon->allowedUsers->pluck('id')->toArray());
                        @endphp
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                    {{ in_array($user->id, $selectedUsers) ? 'selected' : '' }}>
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
                    💾 حفظ التعديلات
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn-cancel">
                    ❌ إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Additional Info Card -->
    <div class="form-card" style="margin-top: 2rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid #3b82f6;">
        <h3 style="color: #1e40af; margin-bottom: 1rem; font-size: 1.2rem;">📊 معلومات إضافية</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>تاريخ الإنشاء</label>
                <input type="text"
                       value="{{ $coupon->created_at->format('Y-m-d H:i') }}"
                       disabled
                       style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>آخر تحديث</label>
                <input type="text"
                       value="{{ $coupon->updated_at->format('Y-m-d H:i') }}"
                       disabled
                       style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            @if($coupon->max_uses)
                <div class="form-group">
                    <label>نسبة الاستخدام</label>
                    <div style="margin-top: 0.5rem;">
                        <div class="usage-bar" style="height: 12px;">
                            <div class="usage-progress" style="width: {{ ($coupon->used_count / $coupon->max_uses) * 100 }}%"></div>
                        </div>
                        <small style="display: block; margin-top: 0.5rem; color: #6b7280;">
                            {{ $coupon->used_count }} / {{ $coupon->max_uses }}
                            ({{ number_format(($coupon->used_count / $coupon->max_uses) * 100, 1) }}%)
                        </small>
                    </div>
                </div>
            @endif

            @if($coupon->allowedUsers->count() > 0)
                <div class="form-group full-width">
                    <label>المستخدمين المسموح لهم حالياً</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;">
                        @foreach($coupon->allowedUsers->take(5) as $user)
                            <span class="type-badge type-percentage">
                                {{ $user->name }}
                            </span>
                        @endforeach
                        @if($coupon->allowedUsers->count() > 5)
                            <span class="type-badge type-fixed">
                                +{{ $coupon->allowedUsers->count() - 5 }} آخرين
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
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

        // تحديث Label عند تحميل الصفحة
        window.addEventListener('DOMContentLoaded', function() {
            updateAmountLabel();
        });
    </script>
@endpush
