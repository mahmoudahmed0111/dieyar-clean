@extends('dashboard.layouts.main')

@section('title', __('trans.chalets_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.chalets_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_chalets_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.chalets.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('trans.add') }}
        </a>
    </div>
</div>

@php
    $columns = [
        '#' => 'id',
        'code' => __('trans.code'),
        'name' => __('trans.name'),
        // 'floor' => __('trans.floor'),
        // 'building' => __('trans.building'),
        // 'location' => __('trans.location'),
        'pass_code' => __('trans.pass_code'),
        // 'status' => __('trans.status'),
        'cleaned' => __('trans.cleaned'),
        'booked' => __('trans.booked'),
        'images' => __('trans.images'),
        'videos' => __('trans.videos'),
        'actions' => __('trans.actions'),
    ];
    $rows = $chalets->map(function($chalet) {
        return [
            '#' => $chalet->id,
            'code' => '<span class="badge badge-info">'.$chalet->code.'</span>',
            'name' => '<div class="user-info">'.$chalet->name.'</div>',
            // 'floor' => $chalet->floor ?: '-',
            // 'building' => $chalet->building ?: '-',
            // 'location' => $chalet->location,
            'pass_code' => $chalet->pass_code,
            // 'status' => '<span class="status-badge status-'.($chalet->status == 'available' ? 'success' : 'danger').'">'.__($chalet->status).'</span>',
            'cleaned' => '<span class="status-badge status-'.($chalet->is_cleaned ? 'success' : 'warning').'">'.($chalet->is_cleaned ? __('trans.yes') : __('trans.no')).'</span>',
            'booked' => '<span class="status-badge status-'.($chalet->is_booked ? 'danger' : 'success').'">'.($chalet->is_booked ? __('trans.yes') : __('trans.no')).'</span>',
            'images' => $chalet->images->count() > 0 
                ? '<a href="'.asset('storage/'.$chalet->images->first()->image).'" class="glightbox btn btn-sm btn-info" data-gallery="chalet-'.$chalet->id.'"><i class="fas fa-images"></i></a>' . 
                  collect($chalet->images)->skip(1)->map(function($img) use ($chalet) {
                      return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="chalet-'.$chalet->id.'" style="display:none;"></a>';
                  })->implode('')
                : '<span class="text-muted">No images</span>',
            'videos' => $chalet->videos->count() > 0 
                ? '<a href="'.asset('storage/'.$chalet->videos->first()->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="chaletv-'.$chalet->id.'"><i class="fas fa-play"></i></a>' . 
                  collect($chalet->videos)->skip(1)->map(function($vid) use ($chalet) {
                      return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox" data-type="video" data-gallery="chaletv-'.$chalet->id.'" style="display:none;"></a>';
                  })->implode('')
                : '<span class="text-muted">No videos</span>',
            'actions' => view('dashboard.chalets._actions', compact('chalet'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$chalets->links('pagination::bootstrap-4')" :empty-message="__('trans.no_chalets_found')" />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize GLightbox for image galleries
    if (typeof GLightbox !== 'undefined') {
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true,
            autoplayVideos: false,
            zoomable: true,
            draggable: true,
            dragToleranceX: 40,
            dragToleranceY: 65,
            preload: true,
            skin: 'clean',
            openEffect: 'zoom',
            closeEffect: 'zoom',
            slideEffect: 'slide',
            closeOnOutsideClick: true,
            startAt: 0,
            width: '90vw',
            height: '90vh',
            descPosition: 'bottom',
            arrows: true,
            counter: true,
            touchNavigation: true,
            keyboardNavigation: true,
            closeButton: true,
            autoplayVideos: false,
            autofocusVideos: false,
            // Add download functionality
            onSlideChange: function({ current, prev }) {
                // Add download button to each slide
                setTimeout(() => {
                    addDownloadButton();
                }, 100);
            },
            onOpen: function() {
                // Add download button when lightbox opens
                setTimeout(() => {
                    addDownloadButton();
                }, 100);
            }
        });

        // Function to add download button
        function addDownloadButton() {
            const lightboxContainer = document.querySelector('.glightbox-container');
            if (lightboxContainer && !lightboxContainer.querySelector('.download-btn')) {
                const currentSlide = lightboxContainer.querySelector('.glightbox-slide.current');
                if (currentSlide) {
                    const img = currentSlide.querySelector('img');
                    if (img) {
                        const downloadBtn = document.createElement('button');
                        downloadBtn.className = 'download-btn';
                        downloadBtn.innerHTML = '<i class="fas fa-download"></i> Download';
                        downloadBtn.style.cssText = `
                            position: absolute;
                            top: 20px;
                            right: 20px;
                            background: rgba(0,0,0,0.7);
                            color: white;
                            border: none;
                            padding: 10px 15px;
                            border-radius: 5px;
                            cursor: pointer;
                            font-size: 14px;
                            z-index: 1000;
                            transition: background 0.3s;
                        `;
                        
                        downloadBtn.addEventListener('mouseenter', function() {
                            this.style.background = 'rgba(0,0,0,0.9)';
                        });
                        
                        downloadBtn.addEventListener('mouseleave', function() {
                            this.style.background = 'rgba(0,0,0,0.7)';
                        });
                        
                        downloadBtn.addEventListener('click', function() {
                            downloadImage(img.src, getImageName(img.src));
                        });
                        
                        lightboxContainer.appendChild(downloadBtn);
                    }
                }
            }
        }

        // Function to download image
        function downloadImage(url, filename) {
            fetch(url)
                .then(response => response.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);
                })
                .catch(error => {
                    console.error('Download failed:', error);
                    // Fallback: open image in new tab
                    window.open(url, '_blank');
                });
        }

        // Function to get image name from URL
        function getImageName(url) {
            const urlParts = url.split('/');
            const filename = urlParts[urlParts.length - 1];
            return filename || 'chalet-image.jpg';
        }
    }
});
</script>
@endsection
