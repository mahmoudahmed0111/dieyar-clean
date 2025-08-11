<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('trans.login') }} - {{ __('trans.dashboard') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/dashboard-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
    @endif
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>{{ $settings->name }}</h1>
                <p>{{ __('trans.login_to_dashboard') }}</p>
            </div>

            <!-- رسائل النجاح -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <!-- رسائل الخطأ العامة -->
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="error-list">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="login-form">
                @csrf

                <div class="form-group">
                    <label for="email">{{ __('trans.email') }}</label>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           required
                           autofocus>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">{{ __('trans.password') }}</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        {{ __('trans.remember_me') }}
                    </label>
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">{{ __('trans.login') }}</span>
                    <span class="btn-loading" style="display: none;">
                        <svg class="spinner" width="20" height="20" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                                <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/>
                                <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/>
                            </circle>
                        </svg>
                        {{ __('trans.loading') }}
                    </span>
                </button>
            </form>

            <div class="login-footer">
                <p>{{ __('trans.test_credentials') }}:</p>
                <div class="test-credentials">
                    <div class="credential-item">
                        <strong>{{ __('trans.email') }}:</strong> info@gmail.com
                    </div>
                    <div class="credential-item">
                        <strong>{{ __('trans.password') }}:</strong> 12345678
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.login-form');
            const submitBtn = form.querySelector('.login-btn');
            const btnText = submitBtn.querySelector('.btn-text');
            const btnLoading = submitBtn.querySelector('.btn-loading');

            form.addEventListener('submit', function() {
                btnText.style.display = 'none';
                btnLoading.style.display = 'flex';
                submitBtn.disabled = true;
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>
