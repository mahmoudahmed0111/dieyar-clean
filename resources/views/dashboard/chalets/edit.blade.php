@extends('dashboard.layouts.main')

@section('title', __('trans.edit_chalet'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
            <a href="{{ route('dashboard.chalets.index') }}" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('trans.back_to_chalets') }}
            </a>
        </div>
        <form action="{{ route('dashboard.chalets.update', $chalet) }}" method="POST" enctype="multipart/form-data" class="user-form">
            @csrf
            @method('PUT')
            <div class="form-header">
                <h3>{{ __('trans.chalet_information') }}</h3>
                <p>{{ __('trans.update_chalet_information_and_media') }}</p>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">{{ __('trans.name') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" class="form-input @error('name') error @enderror" value="{{ old('name', $chalet->name) }}" required placeholder="{{ __('trans.enter_chalet_name') }}">
                    </div>
                    @error('name')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.code') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="code" class="form-input @error('code') error @enderror" value="{{ old('code', $chalet->code) }}" required placeholder="{{ __('trans.enter_chalet_code') }}">
                    </div>
                    @error('code')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.pass_code') }} <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="pass_code" class="form-input @error('pass_code') error @enderror" value="{{ old('pass_code', $chalet->pass_code) }}" required placeholder="{{ __('trans.enter_pass_code') }}">
                    </div>
                    @error('pass_code')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.floor') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="floor" class="form-input @error('floor') error @enderror" value="{{ old('floor', $chalet->floor) }}" placeholder="{{ __('trans.enter_floor') }}">
                    </div>
                    @error('floor')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.building') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="building" class="form-input @error('building') error @enderror" value="{{ old('building', $chalet->building) }}" placeholder="{{ __('trans.enter_building') }}">
                    </div>
                    @error('building')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.location') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="location" class="form-input @error('location') error @enderror" value="{{ old('location', $chalet->location) }}" placeholder="{{ __('trans.enter_location') }}">
                    </div>
                    @error('location')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.type') }}</label>
                    <div class="input-wrapper">
                        <select name="type" class="form-input @error('type') error @enderror">
                            <option value="">{{ __('trans.select_type') }}</option>
                            <option value="apartment" @if(old('type', $chalet->type)=='apartment') selected @endif>{{ __('trans.apartment') }}</option>
                            <option value="studio" @if(old('type', $chalet->type)=='studio') selected @endif>{{ __('trans.studio') }}</option>
                            <option value="villa" @if(old('type', $chalet->type)=='villa') selected @endif>{{ __('trans.villa') }}</option>
                        </select>
                    </div>
                    @error('type')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.status') }}</label>
                    <div class="input-wrapper">
                        <select name="status" class="form-input @error('status') error @enderror" required>
                            <option value="available" @if(old('status', $chalet->status)=='available') selected @endif>{{ __('trans.available') }}</option>
                            <option value="unavailable" @if(old('status', $chalet->status)=='unavailable') selected @endif>{{ __('trans.unavailable') }}</option>
                        </select>
                    </div>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.is_cleaned') }}</label>
                    <div class="input-wrapper">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="is_cleaned" value="1" {{ old('is_cleaned', $chalet->is_cleaned) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            {{ __('trans.mark_as_cleaned') }}
                        </label>
                    </div>
                    @error('is_cleaned')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.is_booked') }}</label>
                    <div class="input-wrapper">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="is_booked" value="1" {{ old('is_booked', $chalet->is_booked) ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            {{ __('trans.mark_as_booked') }}
                        </label>
                    </div>
                    @error('is_booked')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.description') }}</label>
                    <div class="input-wrapper">
                        <textarea name="description" class="form-input @error('description') error @enderror" placeholder="{{ __('trans.enter_description') }}">{{ old('description', $chalet->description) }}</textarea>
                    </div>
                    @error('description')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.add_images') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="images[]" class="form-input @error('images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                        <label class="form-label">{{ __('trans.video_large_chunked_upload') }}</label>
                    <div id="uppy-video-uploader"></div>
                    <input type="hidden" name="uppy_video" id="uploaded_video_path">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_images') }}</label>
                    <div class="input-wrapper">
                        @foreach($chalet->images as $img)
                            <img src="{{ asset('storage/' . $img->image) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_videos') }}</label>
                    <div class="input-wrapper">
                        @foreach($chalet->videos as $vid)
                            <a href="{{ asset('storage/' . $vid->video) }}" target="_blank">{{ __('trans.view') }}</a><br>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="history.back()">{{ __('trans.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('trans.update') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- Uppy CDN JS & CSS -->
<link href="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.css" rel="stylesheet">
<script src="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uppy = new Uppy.Uppy({
        restrictions: {
            maxNumberOfFiles: 1,
            allowedFileTypes: ['video/*']
        },
        autoProceed: true
    })
    .use(Uppy.Dashboard, {
        inline: true,
        target: '#uppy-video-uploader',
        note: '{{ __('trans.only_one_video_unlimited_size_chunked_upload') }}'
    })
    .use(Uppy.XHRUpload, {
        endpoint: "{{ route('dashboard.chalets.uploadVideo') }}",
        fieldName: 'video',
        formData: true,
        bundle: false,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
        }
    });

    uppy.on('complete', (result) => {
        if(result.successful.length > 0) {
            const videoPath = result.successful[0].response.body.path;
            document.getElementById('uploaded_video_path').value = videoPath;
        }
    });
});
</script>
@endsection
