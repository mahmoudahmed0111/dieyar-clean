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
        'type' => __('trans.type'),
        // 'status' => __('trans.status'),
        'cleaned' => __('trans.cleaned'),
        'booked' => __('trans.booked'),
        'images' => __('trans.images'),
        'videos' => __('trans.videos'),
        'actions' => __('trans.actions'),
    ];
    $rows = $chalets->map(function($chalet) {
        $typeLabels = [
            'apartment' => __('trans.apartment'),
            'studio' => __('trans.studio'),
            'villa' => __('trans.villa')
        ];
        return [
            '#' => $chalet->id,
            'code' => '<span class="badge badge-info">'.$chalet->code.'</span>',
            'name' => '<div class="user-info">'.$chalet->name.'</div>',
            // 'floor' => $chalet->floor ?: '-',
            // 'building' => $chalet->building ?: '-',
            // 'location' => $chalet->location,
            'type' => $chalet->type ? $typeLabels[$chalet->type] : '-',
            // 'status' => '<span class="status-badge status-'.($chalet->status == 'available' ? 'success' : 'danger').'">'.__($chalet->status).'</span>',
            'cleaned' => '<span class="status-badge status-'.($chalet->is_cleaned ? 'success' : 'warning').'">'.($chalet->is_cleaned ? __('trans.yes') : __('trans.no')).'</span>',
            'booked' => '<span class="status-badge status-'.($chalet->is_booked ? 'danger' : 'success').'">'.($chalet->is_booked ? __('trans.yes') : __('trans.no')).'</span>',
            'images' => collect($chalet->images)->map(function($img, $i) use ($chalet) {
                return '<a href="'.asset('storage/'.$img->image).'" class="glightbox" data-gallery="chalet-'.$chalet->id.'"><img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;cursor:pointer;" alt="img"></a>';
            })->implode(' '),
            'videos' => collect($chalet->videos)->map(function($vid) use ($chalet) {
                return '<a href="'.asset('storage/'.$vid->video).'" class="glightbox btn btn-sm btn-primary" data-type="video" data-gallery="chaletv-'.$chalet->id.'"><i class="fa fa-play"></i> </a>';
            })->implode('<br>'),
            'actions' => view('dashboard.chalets._actions', compact('chalet'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$chalets->links('pagination::bootstrap-4')" :empty-message="__('trans.no_chalets_found')" />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('{{ __("trans.are_you_sure_you_want_to_delete_this_chalet_this_action_cannot_be_undone") }}')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
