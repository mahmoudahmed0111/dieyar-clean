<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings->name }} - @yield('title', __('trans.dashboard'))</title>

    @include('dashboard.layouts.head')

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <!-- Uppy CSS -->
    <link href="https://releases.transloadit.com/uppy/v3.25.2/uppy.min.css" rel="stylesheet">



    @if (app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('assets/dashboard-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
    @endif

    @yield('styles')
</head>

<body>
    <div class="dashboard-container">
        @include('dashboard.layouts.sidebar')
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        <div class="main-content">
            @include('dashboard.layouts.header')
            <div class="content">
                @yield('content')
            </div>
            @include('dashboard.layouts.footer')
        </div>
    </div>
    @include('dashboard.layouts.scripts')
    @yield('scripts')
</body>

</html>
