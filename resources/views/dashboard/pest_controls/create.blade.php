@extends('dashboard.layouts.main')

@section('title', __('trans.add_pest_control'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
            <a href="{{ route('dashboard.pest_controls.index') }}" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('trans.back_to_pest_controls') }}
            </a>
        </div>
        <form action="{{ route('dashboard.pest_controls.store') }}" method="POST" enctype="multipart/form-data" class="user-form">
            @csrf
            <div class="form-header">
                <h3>{{ __('trans.pest_control_information') }}</h3>
                <p>{{ __('trans.fill_in_the_pest_control_details_below') }}</p>
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
                            <option value="done" @if(old('status')=='done') selected @endif>{{ __('Done') }}</option>
                        </select>
                    </div>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.date') }}</label>
                    <div class="input-wrapper">
                        <input type="date" name="date" class="form-input @error('date') error @enderror" value="{{ old('date') }}">
                    </div>
                    @error('date')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                        <label class="form-label">{{ __('trans.notes') }}</label>
                    <div class="input-wrapper">
                        <textarea name="notes" class="form-input @error('notes') error @enderror">{{ old('notes') }}</textarea>
                    </div>
                    @error('notes')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.images_before_pest_control') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="before_images[]" class="form-input @error('before_images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('before_images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.images_after_pest_control') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="after_images[]" class="form-input @error('after_images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('after_images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.video_before_pest_control_large_chunked_upload') }}</label>
                    <div id="uppy-video-before-uploader"></div>
                    <input type="hidden" name="uppy_video_before" id="uploaded_video_before_path">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.video_after_pest_control_large_chunked_upload') }}</label>
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

<link href="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.css" rel="stylesheet">
<script src="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // فيديو قبل
    const uppyBefore = new Uppy.Uppy({
        restrictions: { maxNumberOfFiles: 1, allowedFileTypes: ['video/*'] },
        autoProceed: true
    })
    .use(Uppy.Dashboard, { inline: true, target: '#uppy-video-before-uploader', note: '{{ __('trans.only_one_video_unlimited_size_chunked_upload') }}' })
    .use(Uppy.XHRUpload, {
        endpoint: "{{ route('dashboard.pest_controls.uploadVideo') }}",
        fieldName: 'video',
        formData: true,
        bundle: false,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
    });
    uppyBefore.on('complete', (result) => {
        if(result.successful.length > 0) {
            document.getElementById('uploaded_video_before_path').value = result.successful[0].response.body.path;
        }
    });
    // فيديو بعد
    const uppyAfter = new Uppy.Uppy({
        restrictions: { maxNumberOfFiles: 1, allowedFileTypes: ['video/*'] },
        autoProceed: true
    })
    .use(Uppy.Dashboard, { inline: true, target: '#uppy-video-after-uploader', note: '{{ __('trans.only_one_video_unlimited_size_chunked_upload') }}' })
    .use(Uppy.XHRUpload, {
        endpoint: "{{ route('dashboard.pest_controls.uploadVideo') }}",
        fieldName: 'video',
        formData: true,
        bundle: false,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content') }
    });
    uppyAfter.on('complete', (result) => {
        if(result.successful.length > 0) {
            document.getElementById('uploaded_video_after_path').value = result.successful[0].response.body.path;
        }
    });
});
</script>
@endsection
