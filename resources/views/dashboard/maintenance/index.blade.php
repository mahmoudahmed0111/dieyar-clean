@extends('dashboard.layouts.main')

@section('title', __('trans.maintenance_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.maintenance_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_maintenance_operations_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.maintenance.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('trans.add_maintenance') }}
        </a>
    </div>
</div>

@php
    $columns = [
        '#' => 'id',
        'chalet' => __('trans.chalet'),
        'cleaner' => __('trans.cleaner'),
        'description' => __('trans.description'),
        'status' => __('trans.status'),
        'requested_at' => __('trans.requested_at'),
        'completed_at' => __('trans.completed_at'),

        'actions' => __('trans.actions'),
    ];
    $rows = $maintenances->map(function($maintenance) {
        return [
            '#' => $maintenance->id,
            'chalet' => $maintenance->chalet?->name ?? '-',
            'cleaner' => $maintenance->cleaner?->name ?? '-',
            'description' => $maintenance->description,
            'status' => '<span class="status-badge status-'.($maintenance->status == 'done' ? 'success' : ($maintenance->status == 'in_progress' ? 'warning' : 'danger')).'">'.__($maintenance->status).'</span>',
            'requested_at' => $maintenance->requested_at,
            'completed_at' => $maintenance->completed_at,
            'actions' => view('dashboard.maintenance._actions', compact('maintenance'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$maintenances->links('pagination::bootstrap-4')" :empty-message="__('trans.no_maintenance_operations_found')" />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Maintenance page specific functionality can be added here
});
</script>
@endsection
