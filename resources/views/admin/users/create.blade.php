@extends('admin.layouts.admin-layout')
@section('title', __('admin.create_user_title'))

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/roles-permissions-styles.css') }}">
@endpush

@section('content')
    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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
        <h2>{{ __('admin.create_user_header') }}</h2>
        <a href="{{ route('admin.users') }}" class="btn-back">
            ← {{ __('admin.back') }}
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
            @csrf

            <div class="form-grid">
                <!-- اسم المستخدم -->
                <div class="form-group">
                    <label for="name">{{ __('admin.full_name') }} {{ __('admin.required_field') }}</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           placeholder="{{ __('admin.enter_user_name') }}"
                           required
                           maxlength="255">
                    @error('name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- البريد الإلكتروني -->
                <div class="form-group">
                    <label for="email">{{ __('admin.email_address') }} {{ __('admin.required_field') }}</label>
                    <input type="email"
                           name="email"
                           id="email"
                           value="{{ old('email') }}"
                           placeholder="example@domain.com"
                           required>
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- رقم الهاتف -->
                <div class="form-group">
                    <label for="phone_number">{{ __('admin.phone_number') }}</label>
                    <input type="text"
                           name="phone_number"
                           id="phone_number"
                           value="{{ old('phone_number') }}"
                           placeholder="{{ __('admin.enter_phone') }}"
                           maxlength="20">
                    @error('phone_number')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- الدور -->
                <div class="form-group">
                    <label for="role_id">{{ __('admin.role') }} {{ __('admin.required_field') }}</label>
                    <select name="role_id" id="role_id" required>
                        <option value="">{{ __('admin.choose_role') }}</option>
                        @foreach ($availableRoles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color: #666;">{{ __('admin.can_assign_owned_roles') }}</small>
                    @error('role_id')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div class="form-group">
                    <label for="password">{{ __('admin.password') }} {{ __('admin.required_field') }}</label>
                    <input type="password"
                           name="password"
                           id="password"
                           placeholder="{{ __('admin.enter_password') }}"
                           required
                           minlength="8">
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <!-- تأكيد كلمة المرور -->
                <div class="form-group">
                    <label for="password_confirmation">{{ __('admin.password_confirmation') }} {{ __('admin.required_field') }}</label>
                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           placeholder="{{ __('admin.reenter_password') }}"
                           required
                           minlength="8">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    {{ __('admin.create_user_btn') }}
                </button>
                <a href="{{ route('admin.users') }}" class="btn-cancel">
                    ✗ {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Form validation
            document.getElementById('createUserForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirmation').value;

                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('{{ __('admin.password_mismatch') }}');
                    return false;
                }

                if (password.length < 8) {
                    e.preventDefault();
                    alert('كلمة المرور يجب أن تكون 8 أحرف على الأقل!');
                    return false;
                }
            });
        </script>
    @endpush
@endsection
