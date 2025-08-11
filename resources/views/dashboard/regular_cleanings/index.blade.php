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
            'images_before' => collect($regularCleaning->images)->where('type','before')->map(function($img, $i) use ($regularCleaning) {
                return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="before-'.$regularCleaning->id.'"><img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;cursor:pointer;" alt="img"></a>';
            })->implode(' '),
            'images_after' => collect($regularCleaning->images)->where('type','after')->map(function($img, $i) use ($regularCleaning) {
                return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="after-'.$regularCleaning->id.'"><img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;cursor:pointer;" alt="img"></a>';
            })->implode(' '),
            'videos_before' => collect($regularCleaning->videos)->where('type','before')->map(function($vid) use ($regularCleaning) {
                return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="vbefore-'.$regularCleaning->id.'"><i class="fa fa-play"></i> '.__('View Video').'</a>';
            })->implode('<br>'),
            'videos_after' => collect($regularCleaning->videos)->where('type','after')->map(function($vid) use ($regularCleaning) {
                return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="vafter-'.$regularCleaning->id.'"><i class="fa fa-play"></i> '.__('View Video').'</a>';
            })->implode('<br>'),
            'actions' => view('dashboard.regular_cleanings._actions', compact('regularCleaning'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$regularCleanings->links('pagination::bootstrap-4')" :empty-message="__('trans.no_regular_cleanings_found')" />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('{{ __("trans.are_you_sure_you_want_to_delete_this_regular_cleaning_this_action_cannot_be_undone") }}')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
