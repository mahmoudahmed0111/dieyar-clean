@extends('dashboard.layouts.main')

@section('title', __('trans.edit_regular_cleaning'))

@section('content')
<div class="form-container">
    <div class="glass-card form-card">
        <div class="form-actions-btn" style="margin-bottom: 1.5rem;">
            <a href="{{ route('dashboard.regular_cleanings.index') }}" class="btn btn-secondary">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('trans.back_to_regular_cleanings') }}
            </a>
        </div>
        <form action="{{ route('dashboard.regular_cleanings.update', $regularCleaning) }}" method="POST" enctype="multipart/form-data" class="user-form">
            @csrf
            @method('PUT')
            <div class="form-header">
                <h3>{{ __('trans.edit_regular_cleaning') }}</h3>
                <p>{{ __('trans.update_regular_cleaning_information_and_media') }}</p>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">{{ __('trans.chalet') }}</label>
                    <div class="input-wrapper">
                        <select name="chalet_id" class="form-input @error('chalet_id') error @enderror">
                            <option value="">{{ __('trans.select_chalet') }}</option>
                            @foreach($chalets as $chalet)
                                <option value="{{ $chalet->id }}" @if(old('chalet_id', $regularCleaning->chalet_id)==$chalet->id) selected @endif>{{ $chalet->name }}</option>
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
                                <option value="{{ $cleaner->id }}" @if(old('cleaner_id', $regularCleaning->cleaner_id)==$cleaner->id) selected @endif>{{ $cleaner->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('cleaner_id')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.date') }}</label>
                    <div class="input-wrapper">
                        <input type="date" name="date" class="form-input @error('date') error @enderror" value="{{ old('date', $regularCleaning->date) }}">
                    </div>
                    @error('date')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.price') }}</label>
                    <div class="input-wrapper">
                        <input type="text" name="price" class="form-input @error('price') error @enderror" value="{{ old('price', $regularCleaning->price) }}" placeholder="{{ __('trans.enter_price') }}">
                    </div>
                    @error('price')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.notes') }}</label>
                    <div class="input-wrapper">
                        <textarea name="notes" class="form-input @error('notes') error @enderror" placeholder="{{ __('trans.enter_notes') }}">{{ old('notes', $regularCleaning->notes) }}</textarea>
                    </div>
                    @error('notes')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.add_images_before_cleaning') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="before_images[]" class="form-input @error('before_images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('before_images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.add_images_after_cleaning') }}</label>
                    <div class="input-wrapper">
                        <input type="file" name="after_images[]" class="form-input @error('after_images') error @enderror" multiple accept="image/*">
                    </div>
                    @error('after_images')<span class="error-message">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.video_before_cleaning_large_chunked_upload') }}</label>
                    <div id="uppy-video-before-uploader"></div>
                    <input type="hidden" name="uppy_video_before" id="uploaded_video_before_path">
                </div>
                <div class="form-group">
                        <label class="form-label">{{ __('trans.video_after_cleaning_large_chunked_upload') }}</label>
                    <div id="uppy-video-after-uploader"></div>
                    <input type="hidden" name="uppy_video_after" id="uploaded_video_after_path">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_images_before_cleaning') }}</label>
                    <div class="input-wrapper">
                        @foreach($regularCleaning->images->where('type','before') as $img)
                            <img src="{{ asset('storage/' . $img->image) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_images_after_cleaning') }}</label>
                    <div class="input-wrapper">
                        @foreach($regularCleaning->images->where('type','after') as $img)
                            <img src="{{ asset('storage/' . $img->image) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_videos_before_cleaning') }}</label>
                    <div class="input-wrapper">
                        @foreach($regularCleaning->videos->where('type','before') as $vid)
                            <a href="{{ asset('storage/' . $vid->video) }}" target="_blank">{{ __('View') }}</a><br>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('trans.current_videos_after_cleaning') }}</label>
                    <div class="input-wrapper">
                        @foreach($regularCleaning->videos->where('type','after') as $vid)
                            <a href="{{ asset('storage/' . $vid->video) }}" target="_blank">{{ __('View') }}</a><br>
                        @endforeach
                    </div>
                </div>
                @php $allInventory = \App\Models\Inventory::all(); $usedInventory = $regularCleaning->inventory; @endphp
                <div class="form-group mt-8">
                    <label class="form-label">{{ __('trans.products_used_inventory') }}</label>
                    <table class="w-full border rounded bg-white">
                        <thead>
                            <tr>
                                <th>{{ __('trans.product') }}</th>
                                <th>{{ __('trans.quantity') }}</th>
                                <th>{{ __('trans.total_price') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="inventory-rows">
                            @forelse($usedInventory as $idx => $used)
                            <tr>
                                <td>
                                    <select name="inventory[{{ $idx }}][id]" class="form-input inventory-product-select" required>
                                        <option value="">{{ __('trans.select_product') }}</option>
                                        @foreach($allInventory as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->price }}" @if($item->id == $used->id) selected @endif>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="1" name="inventory[{{ $idx }}][qty]" class="form-input inventory-qty" placeholder="{{ __('Qty') }}" value="{{ $used->pivot->quantity }}" required>
                                </td>
                                <td>
                                    <input type="text" name="inventory[{{ $idx }}][total]" class="form-input inventory-total" placeholder="0" value="{{ $used->pivot->quantity * $used->price }}" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-remove-row">&times;</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td>
                                    <select name="inventory[0][id]" class="form-input inventory-product-select" required>
                                        <option value="">{{ __('trans.select_product') }}</option>
                                        @foreach($allInventory as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->price }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" min="1" name="inventory[0][qty]" class="form-input inventory-qty" placeholder="{{ __('Qty') }}" required>
                                </td>
                                <td>
                                    <input type="text" name="inventory[0][total]" class="form-input inventory-total" placeholder="0" readonly>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-remove-row" style="display:none">&times;</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-primary mt-2" id="add-inventory-row">{{ __('trans.add_product') }}</button>
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
    // فيديو قبل التنظيف
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
        endpoint: "{{ route('dashboard.regular_cleanings.uploadVideo') }}",
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
    // فيديو بعد التنظيف
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
        endpoint: "{{ route('dashboard.regular_cleanings.uploadVideo') }}",
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIdx = {{ $usedInventory->count() ?: 1 }};
    document.getElementById('add-inventory-row').onclick = function() {
        const tbody = document.getElementById('inventory-rows');
        const newRow = tbody.rows[0].cloneNode(true);
        // تحديث أسماء الحقول
        newRow.querySelectorAll('select, input').forEach(function(el) {
            if (el.name) {
                el.name = el.name.replace(/\d+/, rowIdx);
                if (el.type === 'text' || el.type === 'number') el.value = '';
                if (el.classList.contains('inventory-total')) el.value = '';
            }
        });
        // إظهار زر الحذف
        newRow.querySelector('.btn-remove-row').style.display = '';
        tbody.appendChild(newRow);
        rowIdx++;
    };
    document.getElementById('inventory-rows').addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remove-row')) {
            e.target.closest('tr').remove();
        }
    });
    // حساب السعر النهائي تلقائياً
    document.getElementById('inventory-rows').addEventListener('input', function(e) {
        if (e.target.classList.contains('inventory-qty') || e.target.classList.contains('inventory-product-select')) {
            const row = e.target.closest('tr');
            const select = row.querySelector('.inventory-product-select');
            const qty = row.querySelector('.inventory-qty').value;
            const price = select.options[select.selectedIndex]?.getAttribute('data-price') || 0;
            row.querySelector('.inventory-total').value = qty && price ? (qty * price) : '';
        }
    });
});
</script>
@endsection
