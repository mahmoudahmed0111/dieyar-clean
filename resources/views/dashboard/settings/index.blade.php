@extends('dashboard.layouts.main')

@section('title', __('trans.settings'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-header">
            <h3>{{ __('trans.site_settings') }}</h3>
            <p>{{ __('trans.update_your_site_settings_below') }}</p>
        </div>
        <form action="{{ $settings ? route('dashboard.settings.update', $settings->id) : route('dashboard.settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($settings)
                @method('PUT')
            @endif
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">{{ __('trans.site_name') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="form-input @error('name') error @enderror" value="{{ old('name', $settings?->name) }}" placeholder="{{ __('trans.enter_site_name') }}">
                    </div>
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.phone') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="phone" class="form-input @error('phone') error @enderror" value="{{ old('phone', $settings?->phone) }}" placeholder="{{ __('trans.enter_phone') }}">
                    </div>
                    @error('phone')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.email') }}</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" class="form-input @error('email') error @enderror" value="{{ old('email', $settings?->email) }}" placeholder="{{ __('trans.enter_email') }}">
                    </div>
                    @error('email')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.address') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="address" class="form-input @error('address') error @enderror" value="{{ old('address', $settings?->address) }}" placeholder="{{ __('trans.enter_address') }}">
                    </div>
                    @error('address')<span class="error-message">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('trans.facebook') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="facebook" class="form-input @error('facebook') error @enderror" value="{{ old('facebook', $settings?->facebook) }}" placeholder="{{ __('trans.facebook_link') }}">
                    </div>
                    @error('facebook')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.instagram') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="instagram" class="form-input @error('instagram') error @enderror" value="{{ old('instagram', $settings?->instagram) }}" placeholder="{{ __('trans.instagram_link') }}">
                    </div>
                    @error('instagram')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.twitter') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="twitter" class="form-input @error('twitter') error @enderror" value="{{ old('twitter', $settings?->twitter) }}" placeholder="{{ __('trans.twitter_link') }}">
                    </div>
                    @error('twitter')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.whatsapp') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="whatsapp" class="form-input @error('whatsapp') error @enderror" value="{{ old('whatsapp', $settings?->whatsapp) }}" placeholder="{{ __('trans.whatsapp_number_link') }}">
                    </div>
                    @error('whatsapp')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.logo') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="logo" class="form-input @error('logo') error @enderror" accept="image/*">
                        @if($settings && $settings->logo)
                            <img src="{{ asset('storage/' . $settings->logo) }}" style="width:60px;height:60px;margin-top:10px;object-fit:contain;">
                        @endif
                    </div>
                    @error('logo')<span class="error-message">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('trans.save_settings') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
