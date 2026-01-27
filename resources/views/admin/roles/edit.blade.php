@extends('admin.layouts.admin-layout')
@section('title', 'تعديل الدور - لوحة التحكم')

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
        <h2>✏️ تعديل الدور: {{ $role->name }}</h2>
        <a href="{{ route('admin.roles.index') }}" class="btn-back">
            ← العودة
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" id="roleForm">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <!-- اسم الدور -->
                <div class="form-group">
                    <label for="name">اسم الدور *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}"
                        placeholder="مثال: manager" required maxlength="100">
                    <small>استخدم أحرف صغيرة بدون مسافات</small>
                </div>

                <!-- Guard Name (عرض فقط) -->
                <div class="form-group">
                    <label>Guard Name</label>
                    <input type="text" value="{{ strtoupper($role->guard_name) }}" disabled
                        style="background: #f3f4f6; cursor: not-allowed;">
                    <small>لا يمكن تعديل الـ Guard بعد الإنشاء</small>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <h3>🔐 الصلاحيات (Permissions)</h3>
                <small>اختر الصلاحيات التي تريد إعطاءها لهذا الدور</small>

                @php
                    $selectedPermissions = old('permissions', $role->permissions->pluck('id')->toArray());
                    // ✅ عرض بس الـ permissions اللي عندهم نفس الـ guard
                    $rolePermissions = $permissions->where('guard_name', $role->guard_name);
                @endphp

                <div class="permissions-grid">
                    @if ($rolePermissions->count() > 0)
                        <div class="guard-group">
                            <h4 class="guard-title">
                                @if ($role->guard_name === 'api')
                                    🔌 API Permissions
                                @else
                                    🌐 WEB Permissions
                                @endif
                            </h4>

                            <div class="permissions-list">
                                @foreach ($rolePermissions as $permission)
                                    <label class="permission-checkbox">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}>
                                        <span class="checkbox-label">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p style="text-align: center; color: #ef4444;">
                            لا توجد صلاحيات متاحة لـ {{ strtoupper($role->guard_name) }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    💾 حفظ التعديلات
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn-cancel">
                    ❌ إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Additional Info Card -->
    <div class="form-card"
        style="margin-top: 2rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid #3b82f6;">
        <h3 style="color: #1e40af; margin-bottom: 1rem; font-size: 1.2rem;">📊 معلومات إضافية</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>عدد المستخدمين</label>
                <input type="text" value="{{ $role->users()->count() }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>عدد الصلاحيات الحالية</label>
                <input type="text" value="{{ $role->permissions()->count() }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>تاريخ الإنشاء</label>
                <input type="text" value="{{ $role->created_at->format('Y-m-d H:i') }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>آخر تحديث</label>
                <input type="text" value="{{ $role->updated_at->format('Y-m-d H:i') }}" disabled
                    style="background: #f3f4f6; cursor: not-allowed;">
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/roles-permissions-scripts.js') }}"></script>
@endpush
