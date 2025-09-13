@extends('dashboard.layouts.main')

@section('title', __('trans.cleaners_management'))

@section('content')
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">{{ __('trans.cleaners_management') }}</h1>
            <p class="page-description">{{ __('trans.manage_all_cleaners_and_their_information') }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('dashboard.cleaners.create') }}" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                {{ __('trans.add') }}
            </a>
        </div>
    </div>

    @php
        $columns = [
            '#' => 'id',
            'image' => __('trans.image'),
            'name' => __('trans.name'),
            'national_id' => __('trans.national_id'),
            'address' => __('trans.address'),
            'hire_date' => __('trans.hire_date'),
            'status' => __('trans.status'),
            'actions' => __('trans.actions'),
        ];
        $rows = $cleaners->map(function ($cleaner) {
            return [
                '#' => $cleaner->id,
                'image' =>
                    '<img src="' .
                    ($cleaner->image
                        ? asset('storage/' . $cleaner->image)
                        : 'https://ui-avatars.com/api/?name=' .
                            urlencode($cleaner->name) .
                            '&color=2563eb&background=dbeafe') .
                    '" alt="Cleaner" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">',
                'name' => $cleaner->name,
                'national_id' => $cleaner->national_id,
                'address' => $cleaner->address,
                'hire_date' => $cleaner->hire_date
                    ? \Illuminate\Support\Carbon::parse($cleaner->hire_date)->format('Y-m-d')
                    : '-',
                'status' =>
                    '<span class="status-badge status-' .
                    ($cleaner->status === 'active' ? 'success' : 'danger') .
                    '">' .
                    __($cleaner->status === 'active' ? 'trans.active' : 'trans.inactive') .
                    '</span>',
                'actions' => view('dashboard.cleaners._actions', compact('cleaner'))->render(),
            ];
        });
    @endphp

    <x-table :columns="$columns" :rows="$rows" :pagination="$cleaners->links('pagination::bootstrap-4')" :empty-message="__('trans.no_cleaners_found')" />
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cleaners page specific functionality can be added here
        });
    </script>
@endsection
