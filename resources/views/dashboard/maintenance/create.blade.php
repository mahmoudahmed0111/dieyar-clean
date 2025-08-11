@extends('dashboard.layouts.main')

@section('title', __('trans.add_maintenance'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
            <a href="{{ route('dashboard.maintenance.index') }}" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('trans.back_to_maintenance') }}
            </a>
        </div>
        <form action="{{ route('dashboard.maintenance.store') }}" method="POST" enctype="multipart/form-data" class="user-form">
            @csrf
            <div class="form-header">
                <h3>{{ __('trans.maintenance_information') }}</h3>
                <p>{{ __('trans.fill_in_the_maintenance_details_below') }}</p>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">{{ __('trans.chalet') }}</label>
                    <div class="input-wrapper">
                        <select name="chalet_id" class="form-input @error('chalet_id') error @enderror">
                            <option value="">{{ __('trans.select_chalet') }}</option>
                            @foreach($chalets as $chalet)
                                <option value="{{ $chalet->id }}" @if(old('chalet_id')==$chalet->id) selected @endif>{{ $chalet->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('chalet_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.cleaner') }}</label>
                    <div class="input-wrapper">
                        <select name="cleaner_id" class="form-input @error('cleaner_id') error @enderror">
                            <option value="">{{ __('trans.select_cleaner') }}</option>
                            @foreach($cleaners as $cleaner)
                                <option value="{{ $cleaner->id }}" @if(old('cleaner_id')==$cleaner->id) selected @endif>{{ $cleaner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('cleaner_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                        <label class="form-label">{{ __('trans.description') }}</label>
                    <div class="input-wrapper">
                        <textarea name="description" class="form-input @error('description') error @enderror" placeholder="{{ __('trans.enter_description') }}">{{ old('description') }}</textarea>
                    </div>
                    @error('description')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.status') }}</label>
                    <div class="input-wrapper">
                        <select name="status" class="form-input @error('status') error @enderror">
                            <option value="pending" @if(old('status')=='pending') selected @endif>{{ __('Pending') }}</option>
                            <option value="in_progress" @if(old('status')=='in_progress') selected @endif>{{ __('In Progress') }}</option>
                            <option value="done" @if(old('status')=='done') selected @endif>{{ __('Done') }}</option>
                        </select>
                    </div>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.requested_at') }}</label>
                    <div class="input-wrapper">
                        <input type="datetime-local" name="requested_at" class="form-input @error('requested_at') error @enderror" value="{{ old('requested_at') }}">
                    </div>
                    @error('requested_at')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                            <label class="form-label">{{ __('trans.completed_at') }}</label>
                    <div class="input-wrapper">
                        <input type="datetime-local" name="completed_at" class="form-input @error('completed_at') error @enderror" value="{{ old('completed_at') }}">
                    </div>
                    @error('completed_at')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.images_before_maintenance') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="before_images[]" class="form-input @error('before_images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('before_images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.images_after_maintenance') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="after_images[]" class="form-input @error('after_images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('after_images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.video_before_maintenance_large_chunked_upload') }}</label>
                    <div id="uppy-video-before-uploader"></div>
                    <input type="hidden" name="uppy_video_before" id="uploaded_video_before_path">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.video_after_maintenance_large_chunked_upload') }}</label>
                    <div id="uppy-video-after-uploader"></div>
                    <input type="hidden" name="uppy_video_after" id="uploaded_video_after_path">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="history.back()">{{ __('trans.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('trans.save') }}</button>
            </div>
        </form>
    </div>
</div>

<!-- Uppy CDN JS & CSS -->
<link href="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.css" rel="stylesheet">
<script src="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // فيديو قبل الصيانة
    const uppyBefore = new Uppy.Uppy({
        restrictions: {
            maxNumberOfFiles: 1,
            allowedFileTypes: ['video/*']
        },
        autoProceed: true
    })
    .use(Uppy.Dashboard, {
        inline: true,
        target: '#uppy-video-before-uploader',
        note: '{{ __('trans.only_one_video_unlimited_size_chunked_upload') }}'
    })
    .use(Uppy.XHRUpload, {
        endpoint: "{{ route('dashboard.maintenance.uploadVideo') }}",
        fieldName: 'video',
        formData: true,
        bundle: false,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
        }
    });
    uppyBefore.on('complete', (result) => {
        if(result.successful.length > 0) {
            const videoPath = result.successful[0].response.body.path;
            document.getElementById('uploaded_video_before_path').value = videoPath;
        }
    });
    // فيديو بعد الصيانة
    const uppyAfter = new Uppy.Uppy({
        restrictions: {
            maxNumberOfFiles: 1,
            allowedFileTypes: ['video/*']
        },
        autoProceed: true
    })
    .use(Uppy.Dashboard, {
        inline: true,
        target: '#uppy-video-after-uploader',
            note: '{{ __('trans.only_one_video_unlimited_size_chunked_upload') }}'
    })
    .use(Uppy.XHRUpload, {
        endpoint: "{{ route('dashboard.maintenance.uploadVideo') }}",
        fieldName: 'video',
        formData: true,
        bundle: false,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
        }
    });
    uppyAfter.on('complete', (result) => {
        if(result.successful.length > 0) {
            const videoPath = result.successful[0].response.body.path;
            document.getElementById('uploaded_video_after_path').value = videoPath;
        }
    });
});
</script>
@endsection
