@extends('dashboard.layouts.main')

@section('title', __('trans.add_new_cleaner'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
            <a href="{{ route('dashboard.cleaners.index') }}" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('trans.back_to_cleaners') }}
            </a>
        </div>
        <form method="POST" action="{{ route('dashboard.cleaners.store') }}" class="user-form" enctype="multipart/form-data">
            @csrf
            <div class="form-header">
                <h3>{{ __('trans.cleaner_information') }}</h3>
                <p>{{ __('trans.fill_in_the_cleaner_details_below') }}</p>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">{{ __('trans.full_name') }}<span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input @error('name') error @enderror" placeholder="{{ __('trans.enter_full_name') }}" required>
                    </div>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">{{ __('trans.phone') }}<span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-input @error('phone') error @enderror" placeholder="{{ __('trans.enter_phone_number') }}" required>
                    </div>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">{{ __('trans.email') }}<span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" id="email" name="email" value="{{ old('email') }}" class="form-input @error('email') error @enderror" placeholder="{{ __('trans.enter_email') }}" required>
                    </div>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="national_id" class="form-label">{{ __('trans.national_id') }}</label>
                    <div class="input-wrapper">
                        <input type="text" id="national_id" name="national_id" value="{{ old('national_id') }}" class="form-input @error('national_id') error @enderror" placeholder="{{ __('trans.enter_national_id') }}">
                    </div>
                    @error('national_id')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="address" class="form-label">{{ __('trans.address') }}</label>
                    <div class="input-wrapper">
                        <input type="text" id="address" name="address" value="{{ old('address') }}" class="form-input @error('address') error @enderror" placeholder="{{ __('trans.enter_address') }}">
                    </div>
                    @error('address')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="hire_date" class="form-label">{{ __('trans.hire_date') }}</label>
                    <div class="input-wrapper">
                        <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date') }}" class="form-input @error('hire_date') error @enderror">
                    </div>
                    @error('hire_date')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="status" class="form-label">{{ __('trans.status') }}<span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select id="status" name="status" class="form-input @error('status') error @enderror" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ __('trans.active') }}</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('trans.inactive') }}</option>
                        </select>
                    </div>
                    @error('status')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image" class="form-label">{{ __('trans.cleaner_image') }}</label>
                    <div class="input-wrapper">
                        <input type="file" id="image" name="image" accept="image/*" class="form-input @error('image') error @enderror">
                    </div>
                    @error('image')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="history.back()">
                        {{ __('trans.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('trans.create_cleaner') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('image')?.addEventListener('change', function(e) {
    const [file] = this.files;
    if (file) {
        let imgPreview = document.getElementById('img-preview');
        if (!imgPreview) {
            imgPreview = document.createElement('img');
            imgPreview.id = 'img-preview';
            imgPreview.style.maxWidth = '120px';
            imgPreview.style.marginTop = '1rem';
            this.parentNode.appendChild(imgPreview);
        }
        imgPreview.src = URL.createObjectURL(file);
    }
});
</script>
@endsection
