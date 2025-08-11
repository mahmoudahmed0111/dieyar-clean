@extends('dashboard.layouts.main')

@section('title', __('trans.pest_control_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.pest_control_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_pest_control_operations_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.pest_controls.create') }}" class="btn btn-primary">
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
        'status' => __('trans.status'),
        'images_before' => __('trans.images_before'),
        'images_after' => __('trans.images_after'),
        'videos_before' => __('trans.videos_before'),
        'videos_after' => __('trans.videos_after'),
        'actions' => __('trans.actions'),
    ];
    $rows = $pestControls->map(function($row) {
        return [
            '#' => $row->id,
            'chalet' => $row->chalet?->name ?? '-',
            'cleaner' => $row->cleaner?->name ?? '-',
            'date' => $row->date,
            'status' => '<span class="status-badge status-'.($row->status == 'done' ? 'success' : 'danger').'">'.__($row->status == 'pending' ? 'Pending' : 'Done').'</span>',
            'images_before' => collect($row->images)->where('type','before')->map(function($img){ return '<img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">'; })->implode(' '),
            'images_after' => collect($row->images)->where('type','after')->map(function($img){ return '<img src="'.asset('storage/'.$img->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">'; })->implode(' '),
            'videos_before' => collect($row->videos)->where('type','before')->map(function($vid){ return '<a href="'.asset('storage/'.$vid->video).'" target="_blank">'.__('View').'</a>'; })->implode('<br>'),
            'videos_after' => collect($row->videos)->where('type','after')->map(function($vid){ return '<a href="'.asset('storage/'.$vid->video).'" target="_blank">'.__('View').'</a>'; })->implode('<br>'),
            'actions' => view('dashboard.pest_controls._actions', compact('row'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$pestControls->links('pagination::bootstrap-4')" :empty-message="__('trans.no_pest_control_operations_found')" />
@endsection
