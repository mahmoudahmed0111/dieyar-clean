@extends('dashboard.layouts.main')

@section('title', __('trans.edit_damage'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
            <a href="{{ route('dashboard.damages.index') }}" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('trans.back_to_damages') }}
            </a>
        </div>
        <form action="{{ route('dashboard.damages.update', $damage) }}" method="POST" enctype="multipart/form-data" class="user-form">
            @csrf
            @method('PUT')
            <div class="form-header">
                <h3>{{ __('trans.edit_damage') }}</h3>
                <p>{{ __('trans.update_damage_information_and_media') }}</p>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">{{ __('trans.chalet') }}</label>
                    <div class="input-wrapper">
                        <select name="chalet_id" class="form-input @error('chalet_id') error @enderror">
                            <option value="">{{ __('trans.select_chalet') }}</option>
                            @foreach($chalets as $chalet)
                                <option value="{{ $chalet->id }}" @if(old('chalet_id', $damage->chalet_id)==$chalet->id) selected @endif>{{ $chalet->name }}</option>
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
                                <option value="{{ $cleaner->id }}" @if(old('cleaner_id', $damage->cleaner_id)==$cleaner->id) selected @endif>{{ $cleaner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('cleaner_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.description') }}</label>
                    <div class="input-wrapper">
                        <textarea name="description" class="form-input @error('description') error @enderror" placeholder="{{ __('trans.enter_description') }}">{{ old('description', $damage->description) }}</textarea>
                    </div>
                    @error('description')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.price') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="price" class="form-input @error('price') error @enderror" value="{{ old('price', $damage->price) }}" placeholder="{{ __('trans.enter_price') }}">
                    </div>
                    @error('price')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.status') }}</label>
                    <div class="input-wrapper">
                        <select name="status" class="form-input @error('status') error @enderror">
                            <option value="pending" @if(old('status', $damage->status)=='pending') selected @endif>{{ __('trans.pending') }}</option>
                                <option value="fixed" @if(old('status', $damage->status)=='fixed') selected @endif>{{ __('trans.fixed') }}</option>
                        </select>
                    </div>
                    @error('status')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.reported_at') }}</label>
                    <div class="input-wrapper">
                        <input type="datetime-local" name="reported_at" class="form-input @error('reported_at') error @enderror" value="{{ old('reported_at', $damage->reported_at ? date('Y-m-d\TH:i', strtotime($damage->reported_at)) : '') }}">
                    </div>
                    @error('reported_at')<span class="error-message">{{ $message }}</span>@enderror
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
                        @foreach($damage->images as $img)
                            <img src="{{ asset('storage/' . $img->image) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_videos') }}</label>
                    <div class="input-wrapper">
                        @foreach($damage->videos as $vid)
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
        endpoint: "{{ route('dashboard.damages.uploadVideo') }}",
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
