@extends('dashboard.layouts.main')
@section('title', __('trans.inventory_management'))
@section('content')
<div class="page-header">
    <div class="page-header-content">
        <h1 class="page-title">{{ __('trans.inventory_management') }}</h1>
        <p class="page-description">{{ __('trans.manage_all_inventory_items_and_their_information') }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('dashboard.inventory.create') }}" class="btn btn-primary">
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
        'price' => __('trans.price'),
        'quantity' => __('trans.quantity'),
        'image' => __('trans.image'),
        'actions' => __('trans.actions'),
    ];
    $rows = $items->map(function($item) {
        return [
            '#' => $item->id,
            'name' => $item->name,
            'price' => $item->price,
            'quantity' => $item->quantity,
            'image' => $item->image ? '<img src="'.asset('storage/'.$item->image).'" style="width:40px;height:40px;border-radius:8px;object-fit:cover;" alt="img">' : '',
            'actions' => view('dashboard.inventory._actions', compact('item'))->render(),
        ];
    });
@endphp
<x-table :columns="$columns" :rows="$rows" :pagination="$items->links('pagination::bootstrap-4')" :empty-message="__('trans.no_inventory_items_found')" />
@endsection
