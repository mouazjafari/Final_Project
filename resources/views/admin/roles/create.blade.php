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
                    <small>استخدم أحرف صغيرة بدون مسافات</small>
                </div>

                <!-- Guard Name -->
                <div class="form-group">
                    <label for="guard_name">Guard Name *</label>
                    <select name="guard_name" id="guard_name" required>
                        <option value="">اختر Guard</option>
                        <option value="api" {{ old('guard_name') === 'api' ? 'selected' : '' }}>
                            🔌 API
                        </option>
                        <option value="web" {{ old('guard_name') === 'web' ? 'selected' : '' }}>
                            🌐 WEB
                        </option>
                    </select>
                    <small>API للـ Mobile/API، WEB لـ Admin Panel</small>
                </div>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <h3>🔐 الصلاحيات (Permissions)</h3>
                <small>اختر الصلاحيات التي تريد إعطاءها لهذا الدور</small>

                <div class="permissions-grid">
                    @php
                        $groupedPermissions = $permissions->groupBy('guard_name');
                    @endphp

                    @foreach($groupedPermissions as $guard => $perms)
                        <div class="guard-group">
                            <h4 class="guard-title">
                                @if($guard === 'api')
                                    🔌 API Permissions
                                @else
                                    🌐 WEB Permissions
                                @endif
                            </h4>

                            <div class="permissions-list">
                                @foreach($perms as $permission)
                                    <label class="permission-checkbox">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <span class="checkbox-label">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
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
@endpush
