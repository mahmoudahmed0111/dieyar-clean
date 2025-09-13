@extends('dashboard.layouts.main')

@section('title', __('trans.users_management'))

@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.users_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_system_users_and_their_permissions') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
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
        'name' => __('trans.name'),
        'email' => __('trans.email'),
        'status' => __('trans.status'),
        'created_at' => __('trans.created_at'),
        'actions' => __('trans.actions'),
    ];
    $rows = $users->map(function($user) {
        return [
            '#' => $user->id,
            'name' => '<div class="user-info">  '.$user->name.'</div>',
            'email' => $user->email,
            'status' => '<span class="status-badge status-'.e($user->status_color).'">'.e($user->status_text).'</span>',
            'created_at' => $user->created_at->format('Y-m-d'),
            'actions' => view('dashboard.users._actions', compact('user'))->render(),
        ];
    });
@endphp

<x-table :columns="$columns" :rows="$rows" :pagination="$users->links('pagination::bootstrap-4')" :empty-message="__('trans.no_users_found')" />


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Users page specific functionality can be added here
});
</script>
@endsection
