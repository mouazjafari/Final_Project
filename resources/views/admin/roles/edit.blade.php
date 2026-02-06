@extends('admin.layouts.admin-layout')
@section('title', app()->getLocale() == 'ar' ? 'تعديل الدور - لوحة التحكم' : 'Edit Role - Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/roles-permissions-styles.css') }}">
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
        <h2>✏️ {{ app()->getLocale() == 'ar' ? 'تعديل الدور: ' . $role->name : 'Edit Role: ' . $role->name }}</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-back">
            {{ app()->getLocale() == 'ar' ? '← العودة' : '← Back' }}
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="roleForm">
            @csrf
            @method('PUT')

            <div class="form-grid">
                @php $isAr = app()->getLocale() == 'ar'; @endphp
                <!-- اسم الدور -->
                <div class="form-group">
                    <label for="name">{{ $isAr ? 'اسم الدور *' : 'Role Name *' }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        placeholder="{{ $isAr ? 'مثال: manager' : 'Example: manager' }}" required maxlength="100">
                    <small>{{ $isAr ? 'استخدم أحرف صغيرة بدون مسافات' : 'Use lowercase letters without spaces' }}</small>
                </div>

                <!-- Guard Name (عرض فقط) -->
                <div class="form-group">
                    <label>{{ $isAr ? 'نوع الحماية' : 'Guard Name' }}</label>
                    <input type="text" value="{{ strtoupper($role->guard_name) }}" disabled
                        style="background: #f3f4f6; cursor: not-allowed;">
                    <small>{{ $isAr ? 'لا يمكن تعديل الـ Guard بعد الإنشاء' : 'Guard cannot be modified after creation' }}</small>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <h3>🔐 {{ $isAr ? 'الصلاحيات' : 'Permissions' }}</h3>
                <small>{{ $isAr ? 'اختر الصلاحيات التي تريد إعطاءها لهذا الدور' : 'Select the permissions you want to grant to this role' }}</small>

                @php
                    $selectedPermissions = old('permissions', $role->permissions->pluck('id')->toArray());
                    // ✅ عرض بس الـ permissions اللي عندهم نفس الـ guard
                    $rolePermissions = $permissions->where('guard_name', $role->guard_name);

                    // تجميع الصلاحيات حسب الفئة (بدون dashboard لأنها تلقائية)
                    $categorizedPermissions = [
                        'address' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'address'); }),
                        'coupon' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'coupon'); }),
                        'design' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'design') && !str_contains($p->name, 'option'); }),
                        'invoice' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'invoice'); }),
                        'notification' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'notification'); }),
                        'option' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'design option'); }),
                        'order' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'order'); }),
                        'payment' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'payment'); }),
                        'permission' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'permission'); }),
                        'review' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'review'); }),
                        'role' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'role'); }),
                        'user' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'user') || str_contains($p->name, 'account') || str_contains($p->name, 'profile'); }),
                        'wallet' => $rolePermissions->filter(function($p) { return str_contains($p->name, 'wallet') || str_contains($p->name, 'transaction'); }),
                    ];

                    $isArabic = app()->getLocale() == 'ar';
                    $categoryNames = [
                        'address' => $isArabic ? 'صلاحيات العناوين' : 'Address Permissions',
                        'coupon' => $isArabic ? 'صلاحيات الكوبونات' : 'Coupon Permissions',
                        'design' => $isArabic ? 'صلاحيات التصاميم' : 'Designs Permissions',
                        'invoice' => $isArabic ? 'صلاحيات الفواتير' : 'Invoice Permissions',
                        'notification' => $isArabic ? 'صلاحيات الإشعارات' : 'Notifications Permissions',
                        'option' => $isArabic ? 'صلاحيات خيارات التصميم' : 'Options Permissions',
                        'order' => $isArabic ? 'صلاحيات الطلبات' : 'Order Permissions',
                        'payment' => $isArabic ? 'صلاحيات الدفع' : 'Payment Permissions',
                        'permission' => $isArabic ? 'إدارة الصلاحيات' : 'Permission Management',
                        'review' => $isArabic ? 'صلاحيات التقييمات' : 'Review Permissions',
                        'role' => $isArabic ? 'صلاحيات الأدوار' : 'Role Permissions',
                        'user' => $isArabic ? 'صلاحيات المستخدمين' : 'User Permissions',
                        'wallet' => $isArabic ? 'صلاحيات المحفظة' : 'Wallet Permissions',
                    ];
                @endphp

                <div class="permissions-grid">
                    @if ($rolePermissions->count() > 0)
                        @foreach($categorizedPermissions as $categoryKey => $perms)
                            @if($perms->count() > 0)
                                <div class="permission-category">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                        <h4 style="margin: 0; color: #2c3e50; font-size: 0.95rem;">
                                            🛡️ {{ $categoryNames[$categoryKey] }}
                                        </h4>
                                        @php
                                            $selectedCount = $perms->filter(function($p) use ($selectedPermissions) {
                                                return in_array($p->id, $selectedPermissions);
                                            })->count();
                                        @endphp
                                        <small style="color: #6b7280;">({{ $selectedCount }}/{{ $perms->count() }})</small>
                                    </div>

                                    <div class="permissions-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @foreach($perms as $permission)
                                            <label class="permission-badge">
                                                <input type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission->id }}"
                                                       class="permission-input"
                                                       {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>
                                                <span class="badge-text">✓ {{ $permission->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p style="text-align: center; color: #ef4444;">
                            {{ $isAr ? 'لا توجد صلاحيات متاحة لـ ' . strtoupper($role->guard_name) : 'No permissions available for ' . strtoupper($role->guard_name) }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    💾 {{ $isAr ? 'حفظ التعديلات' : 'Save Changes' }}
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
                    ❌ {{ $isAr ? 'إلغاء' : 'Cancel' }}
                </a>
            </div>
        </form>
    </div>

    <!-- Additional Info Card -->
    <div class="form-card"
        style="margin-top: 2rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid #3b82f6;">
        <h3 style="color: #1e40af; margin-bottom: 1rem; font-size: 1.2rem;">📊 {{ $isAr ? 'معلومات إضافية' : 'Additional Information' }}</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>{{ $isAr ? 'عدد المستخدمين' : 'Number of Users' }}</label>
                <input type="text" value="{{ $role->users()->count() }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>{{ $isAr ? 'عدد الصلاحيات الحالية' : 'Current Permissions Count' }}</label>
                <input type="text" value="{{ $role->permissions()->count() }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>{{ $isAr ? 'تاريخ الإنشاء' : 'Created At' }}</label>
                <input type="text" value="{{ $role->created_at->format('Y-m-d H:i') }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>{{ $isAr ? 'آخر تحديث' : 'Last Updated' }}</label>
                <input type="text" value="{{ $role->updated_at->format('Y-m-d H:i') }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/roles-permissions-scripts.js') }}"></script>
@endpush
