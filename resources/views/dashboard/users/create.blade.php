@extends('dashboard.layouts.main')

@section('title', __('trans.add_new_user'))

@section('content')


    <div class="form-container">
        <div class="glass-card form-card">
            <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
                <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('trans.back_to_users') }}
                </a>
            </div>
            <form method="POST" action="{{ route('dashboard.users.store') }}" class="user-form">
                @csrf

                <div class="form-header">
                    <h3>{{ __('trans.user_information') }}</h3>
                    <p>{{ __('trans.fill_in_the_user_details_below') }}</p>
                </div>

                <div class="form-grid">
                    <!-- Name Field -->
                    <div class="form-group">
                        <label for="name" class="form-label">

                            {{ __('trans.full_name') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="form-input @error('name') error @enderror" placeholder="{{ __('trans.enter_full_name') }}"
                                required>
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email" class="form-label">

                            {{ __('trans.email_address') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="form-input @error('email') error @enderror"
                                placeholder="{{ __('trans.enter_email_address') }}" required>
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <label for="password" class="form-label">

                            {{ __('trans.password') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password"
                                class="form-input @error('password') error @enderror"
                                placeholder="{{ __('trans.enter_password') }}" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24" class="eye-icon">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        <div class="password-strength" id="password-strength"></div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Confirmation Field -->
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="input-icon">
                                <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            {{ __('trans.confirm_password') }}
                            <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input @error('password_confirmation') error @enderror"
                                    placeholder="{{ __('trans.confirm_password') }}" required>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('password_confirmation')">
                                <svg width="20" height="20" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24" class="eye-icon">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Role Information -->

                <!-- Form Actions -->
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        {{ __('trans.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('trans.create_user') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const toggle = field.nextElementSibling;
            const icon = toggle.querySelector('.eye-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML =
                    '<path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/>';
            } else {
                field.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');

            let strength = 0;
            let feedback = '';

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    feedback = '<span class="strength-weak">{{ __('trans.very_weak') }}</span>';
                    break;
                case 2:
                    feedback = '<span class="strength-fair">{{ __('trans.weak') }}</span>';
                    break;
                case 3:
                    feedback = '<span class="strength-good">{{ __('trans.fair') }}</span>';
                    break;
                case 4:
                    feedback = '<span class="strength-strong">{{ __('trans.good') }}</span>';
                    break;
                case 5:
                    feedback = '<span class="strength-very-strong">{{ __('trans.very_strong') }}</span>';
                    break;
            }

            strengthDiv.innerHTML = feedback;
        });

        // Form validation
        document.querySelector('.user-form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('{{ __('trans.passwords_do_not_match') }}');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('{{ __('trans.password_must_be_at_least_8_characters_long') }}');
                return false;
            }
        });
    </script>
@endsection
