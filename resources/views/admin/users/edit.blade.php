@extends('admin.layouts.admin-layout')
@section('title', __('admin.edit_user_role_title'))

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
        <h2>{{ __('admin.edit_user_role_header') }} {{ $user->name }}</h2>
        <a href="{{ route('admin.users') }}" class="btn-back">
            ← {{ __('admin.back') }}
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <div class="user-info-box" style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <h3 style="margin: 0 0 15px 0; color: #2c3e50;">{{ __('admin.user_info') }}</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div>
                    <strong>{{ __('admin.name') }}:</strong> {{ $user->name }}
                </div>
                <div>
                    <strong>{{ __('admin.email_address') }}:</strong> {{ $user->email }}
                </div>
                <div>
                    <strong>{{ __('admin.phone_number') }}:</strong> {{ $user->phone_number ?? __('admin.not_specified') }}
                </div>
                <div>
                    <strong>{{ __('admin.current_role') }}:</strong>
                    <span style="background: #3498db; color: white; padding: 4px 12px; border-radius: 4px; font-size: 14px;">
                        {{ $user->roles->first()->name ?? __('admin.no_role') }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="role_id">{{ __('admin.new_role') }} {{ __('admin.required_field') }}</label>
                <select name="role_id" id="role_id" required style="width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ddd; border-radius: 6px;">
                    <option value="">{{ __('admin.choose_role') }}</option>
                    @foreach ($availableRoles as $role)
                        <option value="{{ $role->id }}"
                                {{ $user->roles->first() && $user->roles->first()->id == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <small style="color: #666; display: block; margin-top: 8px;">
                    {{ __('admin.warning_owned_roles') }}
                </small>
                @error('role_id')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    {{ __('admin.save_changes') }}
                </button>
                <a href="{{ route('admin.users') }}" class="btn-cancel">
                    ✗ {{ __('admin.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection
