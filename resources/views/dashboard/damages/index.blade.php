@extends('dashboard.layouts.main')

@section('title', __('trans.damages_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.damages_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_damages_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.damages.create') }}" class="btn btn-primary">
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
        'description' => __('trans.description'),
        'price' => __('trans.price'),
        'status' => __('trans.status'),
        'images' => __('trans.images'),
        'videos' => __('trans.videos'),
        'actions' => __('trans.actions'),
    ];
    $rows = $damages->map(function($damage) {
        return [
            '#' => $damage->id,
            'chalet' => $damage->chalet?->name ?? '-',
            'cleaner' => $damage->cleaner?->name ?? '-',
            'description' => $damage->description,
            'price' => $damage->price,
            'status' => '<span class="status-badge status-'.($damage->status == 'pending' ? 'danger' : 'success').'">'.__($damage->status).'</span>',
            'images' => collect($damage->images)->map(function($img){ return '<img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">'; })->implode(' '),
            'videos' => collect($damage->videos)->map(function($vid){ return '<a href="'.asset('storage/'.$vid->video).'" target="_blank"><i class="fa fa-play"></i></a>'; })->implode('<br>'),
            'actions' => view('dashboard.damages._actions', compact('damage'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$damages->links('pagination::bootstrap-4')" :empty-message="__('trans.no_damages_found')" />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm('{{ __("trans.are_you_sure_you_want_to_delete_this_damage_this_action_cannot_be_undone") }}')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endsection
