@extends('admin.layouts.admin-layout')
@section('title', app()->getLocale() == 'ar' ? 'إنشاء دور جديد - لوحة التحكم' : 'Create New Role - Dashboard')

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
        <h2>➕ {{ app()->getLocale() == 'ar' ? 'إنشاء دور جديد' : 'Create New Role' }}</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-back">
            {{ app()->getLocale() == 'ar' ? '← العودة' : '← Back' }}
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.roles.store') }}" method="POST" id="roleForm">
            @csrf

            <div class="form-grid">
                @php $isAr = app()->getLocale() == 'ar'; @endphp
                <!-- اسم الدور -->
                <div class="form-group">
                    <label for="name">{{ $isAr ? 'اسم الدور *' : 'Role Name *' }}</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           placeholder="{{ $isAr ? 'مثال: manager' : 'Example: manager' }}"
                           required
                           maxlength="100">
                    <small>{{ $isAr ? 'استخدم أحرف صغيرة بدون مسافات (مثل: admin, manager, editor)' : 'Use lowercase letters without spaces (e.g., admin, manager, editor)' }}</small>
                    <small style="display: block; margin-top: 5px; color: #3498db;">
                        🌐 {{ $isAr ? 'سيتم إنشاء الدور تلقائياً لـ WEB (لوحة التحكم)' : 'Role will be automatically created for WEB (Dashboard)' }}
                    </small>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0;">🔐 {{ $isAr ? 'الصلاحيات' : 'Permissions' }}</h3>
                        <small style="color: #6b7280;">{{ $isAr ? 'اختر الصلاحيات التي تريد إعطاءها لهذا الدور' : 'Select the permissions you want to grant to this role' }}</small>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn-secondary" onclick="selectAllPermissions()">
                            {{ $isAr ? '✅ تحديد الكل' : '✅ Select All' }}
                        </button>
                        <button type="button" class="btn-secondary" onclick="deselectAllPermissions()">
                            {{ $isAr ? '❌ إلغاء التحديد' : '❌ Deselect All' }}
                        </button>
                    </div>
                </div>

                <div class="permissions-grid">
                    @php
                        // تجميع الصلاحيات حسب الفئة (بدون dashboard لأنها تلقائية)
                        $categorizedPermissions = [
                            'address' => $permissions->filter(function($p) { return str_contains($p->name, 'address'); }),
                            'coupon' => $permissions->filter(function($p) { return str_contains($p->name, 'coupon'); }),
                            'design' => $permissions->filter(function($p) { return str_contains($p->name, 'design') && !str_contains($p->name, 'option'); }),
                            'invoice' => $permissions->filter(function($p) { return str_contains($p->name, 'invoice'); }),
                            'notification' => $permissions->filter(function($p) { return str_contains($p->name, 'notification'); }),
                            'option' => $permissions->filter(function($p) { return str_contains($p->name, 'design option'); }),
                            'order' => $permissions->filter(function($p) { return str_contains($p->name, 'order'); }),
                            'payment' => $permissions->filter(function($p) { return str_contains($p->name, 'payment'); }),
                            'permission' => $permissions->filter(function($p) { return str_contains($p->name, 'permission'); }),
                            'review' => $permissions->filter(function($p) { return str_contains($p->name, 'review'); }),
                            'role' => $permissions->filter(function($p) { return str_contains($p->name, 'role'); }),
                            'user' => $permissions->filter(function($p) { return str_contains($p->name, 'user') || str_contains($p->name, 'account') || str_contains($p->name, 'profile'); }),
                            'wallet' => $permissions->filter(function($p) { return str_contains($p->name, 'wallet') || str_contains($p->name, 'transaction'); }),
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

                    @foreach($categorizedPermissions as $categoryKey => $perms)
                        @if($perms->count() > 0)
                            <div class="permission-category">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h4 style="margin: 0; color: #2c3e50; font-size: 0.95rem;">
                                        🛡️ {{ $categoryNames[$categoryKey] }}
                                    </h4>
                                    <small style="color: #6b7280;">({{ $perms->count() }}/{{ $perms->count() }})</small>
                                </div>

                                <div class="permissions-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                    @foreach($perms as $permission)
                                        <label class="permission-badge">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   class="permission-input"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <span class="badge-text">✓ {{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if($permissions->count() === 0)
                    <div style="text-align: center; padding: 3rem; background: #f9fafb; border-radius: 12px; margin-top: 1rem;">
                        <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🔐</div>
                        <h3 style="color: #6b7280;">لا توجد صلاحيات متاحة</h3>
                        <p style="color: #9ca3af; margin-top: 0.5rem;">يرجى إنشاء صلاحيات أولاً</p>
                    </div>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    💾 حفظ الدور
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
                    ❌ إلغاء
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/translations.js') }}"></script>
    <script src="{{ asset('js/roles-permissions-scripts.js') }}"></script>
    <script>

            });
        }

        function deselectAllPermissions() {
            document.querySelectorAll('.permission-input').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        // Form Validation
        document.getElementById('roleForm')?.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();

            if (!name) {
                e.preventDefault();
                alert('⚠️ الرجاء إدخال اسم الدور');
                document.getElementById('name').focus();
                return false;
            }

            // Check if name contains spaces or special characters
            if (!/^[a-z0-9_-]+$/.test(name)) {
                e.preventDefault();
                alert('⚠️ اسم الدور يجب أن يحتوي على أحرف صغيرة وأرقام فقط بدون مسافات');
                document.getElementById('name').focus();
                return false;
            }
        });

        // Auto-lowercase role name
        document.getElementById('name')?.addEventListener('input', function() {
            this.value = this.value.toLowerCase().replace(/\s+/g, '-');
        });

        console.log('✅ Role Create JavaScript loaded successfully!');
    </script>
@endpush
