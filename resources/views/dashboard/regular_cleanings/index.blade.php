@extends('dashboard.layouts.main')

@section('title', __('trans.regular_cleanings_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.regular_cleanings_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_regular_cleanings_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.regular_cleanings.create') }}" class="btn btn-primary">
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
        'chalet' => __('trans.chalet'),
        'cleaner' => __('trans.cleaner'),
        'date' => __('trans.date'),
        'price' => __('trans.price'),
        'images_before' => __('trans.images_before'),
        'images_after' => __('trans.images_after'),
        'videos_before' => __('trans.videos_before'),
        'videos_after' => __('trans.videos_after'),
        'actions' => __('trans.actions'),
    ];
    $rows = $regularCleanings->map(function($regularCleaning) {
        return [
            '#' => $regularCleaning->id,
            'chalet' => $regularCleaning->chalet?->name ?? '-',
            'cleaner' => $regularCleaning->cleaner?->name ?? '-',
            'date' => $regularCleaning->date,
            'price' => $regularCleaning->price,
            'images_before' => collect($regularCleaning->images)->where('type','before')->count() > 0 
                ? '<a href="'.asset('storage/'.collect($regularCleaning->images)->where('type','before')->first()->image).'" class="glightbox btn btn-sm btn-info" data-gallery="before-'.$regularCleaning->id.'"><i class="fas fa-images"></i></a>' . 
                  collect($regularCleaning->images)->where('type','before')->skip(1)->map(function($img) use ($regularCleaning) {
                      return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="before-'.$regularCleaning->id.'" style="display:none;"></a>';
                  })->implode('')
                : '<span class="text-muted">No images</span>',
            'images_after' => collect($regularCleaning->images)->where('type','after')->count() > 0 
                ? '<a href="'.asset('storage/'.collect($regularCleaning->images)->where('type','after')->first()->image).'" class="glightbox btn btn-sm btn-info" data-gallery="after-'.$regularCleaning->id.'"><i class="fas fa-images"></i></a>' . 
                  collect($regularCleaning->images)->where('type','after')->skip(1)->map(function($img) use ($regularCleaning) {
                      return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="after-'.$regularCleaning->id.'" style="display:none;"></a>';
                  })->implode('')
                : '<span class="text-muted">No images</span>',
            'videos_before' => collect($regularCleaning->videos)->where('type','before')->count() > 0 
                ? '<a href="'.asset('storage/'.collect($regularCleaning->videos)->where('type','before')->first()->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="vbefore-'.$regularCleaning->id.'"><i class="fas fa-play"></i></a>' . 
                  collect($regularCleaning->videos)->where('type','before')->skip(1)->map(function($vid) use ($regularCleaning) {
                      return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox" data-type="video" data-gallery="vbefore-'.$regularCleaning->id.'" style="display:none;"></a>';
                  })->implode('')
                : '<span class="text-muted">No videos</span>',
            'videos_after' => collect($regularCleaning->videos)->where('type','after')->count() > 0 
                ? '<a href="'.asset('storage/'.collect($regularCleaning->videos)->where('type','after')->first()->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="vafter-'.$regularCleaning->id.'"><i class="fas fa-play"></i></a>' . 
                  collect($regularCleaning->videos)->where('type','after')->skip(1)->map(function($vid) use ($regularCleaning) {
                      return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox" data-type="video" data-gallery="vafter-'.$regularCleaning->id.'" style="display:none;"></a>';
                  })->implode('')
                : '<span class="text-muted">No videos</span>',
            'actions' => view('dashboard.regular_cleanings._actions', compact('regularCleaning'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$regularCleanings->links('pagination::bootstrap-4')" :empty-message="__('trans.no_regular_cleanings_found')" />
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
            onSlideChange: function({ current, prev }) {
                setTimeout(() => {
                    addDownloadButton();
                }, 100);
            },
            onOpen: function() {
                setTimeout(() => {
                    addDownloadButton();
                }, 100);
            }
        });

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
                    window.open(url, '_blank');
                });
        }

        function getImageName(url) {
            const urlParts = url.split('/');
            const filename = urlParts[urlParts.length - 1];
            return filename || 'regular-cleaning-image.jpg';
        }
    }
});
</script>
@endsection
