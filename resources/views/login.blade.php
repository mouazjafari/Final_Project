<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل دخول الإدارة - متجر الملابس</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 35px 30px;
            text-align: center;
        }

        .header-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 13px;
            opacity: 0.9;
            font-weight: 300;
        }

        .form-container {
            padding: 40px 35px;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            text-align: center;
            font-size: 22px;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 22px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 13px 15px;
            border: 1.5px solid #dfe4ea;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
            background: #f8f9fa;
        }

        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="text"]:focus {
            border-color: #2c3e50;
            background: white;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.05);
        }

        input::placeholder {
            color: #aaa;
        }

        .password-toggle {
            position: absolute;
            left: 15px;
            top: 40px;
            cursor: pointer;
            color: #7f8c8d;
            font-size: 18px;
            user-select: none;
            transition: color 0.3s;
        }

        .password-toggle:hover {
            color: #2c3e50;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(44, 62, 80, 0.25);
        }

        .btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn.loading {
            position: relative;
            color: transparent;
        }

        .btn.loading::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spinner 0.6s linear infinite;
        }

        @keyframes spinner {
            to {
                transform: rotate(360deg);
            }
        }

        .error {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 6px;
            display: none;
            animation: shake 0.3s ease;
        }

        .error.show {
            display: block;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d5f4e6;
            color: #0c6b3f;
            border: 1px solid #a8e6cf;
        }

        .alert-danger {
            background: #ffe5e5;
            color: #c0392b;
            border: 1px solid #ffcccc;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 18px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #555;
            cursor: pointer;
            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            margin: 0;
            padding: 0;
        }

        .forgot-password {
            color: #2c3e50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            text-decoration: underline;
            color: #1a252f;
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 13px;
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 35px 25px;
            }

            .header {
                padding: 30px 20px;
            }

            h2 {
                font-size: 20px;
            }

            .header h1 {
                font-size: 22px;
            }

            .remember-forgot {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">🔐</div>
            <h1>لوحة التحكم</h1>
            <p>تسجيل دخول المسؤولين</p>
        </div>

        <div class="form-container">
            <h2>مرحباً بك</h2>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST" id="loginForm">
                @csrf

                <div class="input-group">
                    <label for="email">البريد الإلكتروني</label>
                    <input type="email" name="email" id="email" placeholder="admin@example.com"
                        value="{{ old('email') }}" required autofocus autocomplete="email">
                    @error('email')
                        <span class="error show">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group">
                    <label for="password">كلمة المرور</label>
                    <input type="password" name="password" id="password" placeholder="••••••••" required
                        autocomplete="current-password">
                    <span class="password-toggle" onclick="togglePassword()" title="إظهار/إخفاء كلمة المرور">👁️</span>
                    @error('password')
                        <span class="error show">{{ $message }}</span>
                    @enderror
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember" value="1"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span>تذكرني</span>
                    </label>
                    {{-- <a href="{{ route('admin.password.request') }}" class="forgot-password">نسيت كلمة المرور؟</a> --}}
                </div>

                <button type="submit" class="btn" id="submitBtn">تسجيل الدخول</button>
            </form>

            <div class="footer-text">
                © {{ date('Y') }} متجر الأناقة - جميع الحقوق محفوظة
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const input = document.getElementById('password');
            const toggle = document.querySelector('.password-toggle');

            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = '🙈';
                toggle.title = 'إخفاء كلمة المرور';
            } else {
                input.type = 'password';
                toggle.textContent = '👁️';
                toggle.title = 'إظهار كلمة المرور';
            }
        }

        // Remember Me Checkbox Handler
        document.querySelector('.remember-me')?.addEventListener('click', function(e) {
            if (e.target.tagName !== 'INPUT') {
                const checkbox = this.querySelector('input');
                checkbox.checked = !checkbox.checked;
            }
        });

        // Form Submit Handler with Loading State
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        form?.addEventListener('submit', function(e) {
            // Prevent double submission
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            // Add loading state
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'جاري تسجيل الدخول...';

            // Re-enable after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = 'تسجيل الدخول';
            }, 5000);
        });

        // Auto hide alerts after 5 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });

        // Enter key on password field
        document.getElementById('password')?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                form.submit();
            }
        });
    </script>
</body>

</html>
