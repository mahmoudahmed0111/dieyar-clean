@extends('dashboard.layouts.main')

@section('title', __('trans.deep_cleanings_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.deep_cleanings_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_deep_cleanings_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.deep_cleanings.create') }}" class="btn btn-primary">
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
    $rows = $deepCleanings->map(function($deepCleaning) {
        return [
            '#' => $deepCleaning->id,
            'chalet' => $deepCleaning->chalet?->name ?? '-',
            'cleaner' => $deepCleaning->cleaner?->name ?? '-',
            'date' => $deepCleaning->date,
            'price' => $deepCleaning->price,
            'images_before' => collect($deepCleaning->images)->where('type','before')->map(function($img, $i) use ($deepCleaning) {
                return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="before-'.$deepCleaning->id.'"><img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;cursor:pointer;" alt="img"></a>';
            })->implode(' '),
            'images_after' => collect($deepCleaning->images)->where('type','after')->map(function($img, $i) use ($deepCleaning) {
                return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="after-'.$deepCleaning->id.'"><img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;cursor:pointer;" alt="img"></a>';
            })->implode(' '),
            'videos_before' => collect($deepCleaning->videos)->where('type','before')->map(function($vid) use ($deepCleaning) {
                return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="vbefore-'.$deepCleaning->id.'"><i class="fa fa-play"></i> '.__('View Video').'</a>';
            })->implode('<br>'),
            'videos_after' => collect($deepCleaning->videos)->where('type','after')->map(function($vid) use ($deepCleaning) {
                return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="vafter-'.$deepCleaning->id.'"><i class="fa fa-play"></i> '.__('View Video').'</a>';
            })->implode('<br>'),
            'actions' => view('dashboard.deep_cleanings._actions', compact('deepCleaning'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$deepCleanings->links('pagination::bootstrap-4')" :empty-message="__('trans.no_deep_cleanings_found')" />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('{{ __("trans.are_you_sure_you_want_to_delete_this_deep_cleaning_this_action_cannot_be_undone") }}')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
