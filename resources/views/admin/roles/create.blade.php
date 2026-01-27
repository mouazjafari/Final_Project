@extends('admin.layouts.admin-layout')
@section('title', 'إنشاء دور جديد - لوحة التحكم')

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
        <h2>➕ إنشاء دور جديد</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-back">
            ← العودة
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.roles.store') }}" method="POST" id="roleForm">
            @csrf

            <div class="form-grid">
                <!-- اسم الدور -->
                <div class="form-group">
                    <label for="name">اسم الدور *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           placeholder="مثال: manager"
                           required
                           maxlength="100">
                    <small>استخدم أحرف صغيرة بدون مسافات (مثل: admin, manager, editor)</small>
                </div>

                <!-- Guard Name -->
                <div class="form-group">
                    <label for="guard_name">Guard Name *</label>
                    <select name="guard_name" id="guard_name" required>
                        <option value="">اختر Guard</option>
                        <option value="api" {{ old('guard_name') === 'api' ? 'selected' : '' }}>
                            🔌 API - للتطبيقات والموبايل
                        </option>
                        <option value="web" {{ old('guard_name') === 'web' ? 'selected' : '' }}>
                            🌐 WEB - لوحة التحكم
                        </option>
                    </select>
                    <small>اختر API للمستخدمين عبر التطبيق، أو WEB للوحة التحكم</small>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0;">🔐 الصلاحيات (Permissions)</h3>
                        <small style="color: #6b7280;">اختر الصلاحيات التي تريد إعطاءها لهذا الدور</small>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn-secondary" onclick="selectAllPermissions()">
                            ✅ تحديد الكل
                        </button>
                        <button type="button" class="btn-secondary" onclick="deselectAllPermissions()">
                            ❌ إلغاء التحديد
                        </button>
                    </div>
                </div>

                <div class="permissions-grid">
                    @php
                        $groupedPermissions = $permissions->groupBy('guard_name');
                    @endphp

                    @foreach($groupedPermissions as $guard => $perms)
                        <div class="guard-group">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <h4 class="guard-title" style="margin: 0;">
                                    @if($guard === 'api')
                                        🔌 API Permissions
                                    @else
                                        🌐 WEB Permissions
                                    @endif
                                </h4>
                                <small style="color: #6b7280;">{{ $perms->count() }} صلاحية</small>
                            </div>

                            <div class="permissions-list">
                                @foreach($perms as $permission)
                                    <label class="permission-checkbox">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               class="permission-input guard-{{ $guard }}"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <span class="checkbox-label">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
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
    <script src="{{ asset('js/roles-permissions-scripts.js') }}"></script>
    <script>
        // Select/Deselect All Permissions
        function selectAllPermissions() {
            document.querySelectorAll('.permission-input').forEach(checkbox => {
                checkbox.checked = true;
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
            const guardName = document.getElementById('guard_name').value;

            if (!name) {
                e.preventDefault();
                alert('⚠️ الرجاء إدخال اسم الدور');
                document.getElementById('name').focus();
                return false;
            }

            if (!guardName) {
                e.preventDefault();
                alert('⚠️ الرجاء اختيار Guard Name');
                document.getElementById('guard_name').focus();
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
