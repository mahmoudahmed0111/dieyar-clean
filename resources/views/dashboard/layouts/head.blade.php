<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="لوحة تحكم {{ $settings->name }} - إدارة خدمات النظافة والعقارات">
<meta name="keywords" content="نظافة, عقارات, صيانة, إدارة">
<meta name="author" content="{{ $settings->name }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<meta property="og:title" content="@yield('title', 'لوحة التحكم')">
<meta property="og:description" content="لوحة تحكم {{ $settings->name }}">
<meta property="og:type" content="website">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="theme-color" content="#2563eb">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $settings->name }}">

<!-- Preload critical resources -->
<link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" as="style">
<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" as="style">
<link rel="preload" href="{{ asset('assets/dashboard.css') }}" as="style">
<link rel="preload" href="{{ asset('assets/dashboard-rtl.css') }}" as="style">

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<!-- Font Awesome 6 Free -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">
<!-- Font Awesome Fallback -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.5.1/css/all.css" crossorigin="anonymous">

<!-- GLightbox for image/video lightbox -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

<!-- SweetAlert2 for beautiful alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Custom CSS -->
@if (app()->getLocale() == 'ar')
    <link rel="stylesheet" href="{{ asset('assets/dashboard-rtl.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('assets/dashboard.css') }}">
@endif

<!-- Font Awesome Fix -->
<style>
/* Ensure Font Awesome icons load properly */
.fa, .fas, .far, .fal, .fab {
    font-family: "Font Awesome 6 Free" !important;
    font-weight: 900;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

.far {
    font-weight: 400;
}

.fab {
    font-weight: 400;
}

/* Button icon spacing */
.btn i {
    margin-right: 0.25rem;
}

.btn i:only-child {
    margin-right: 0;
}

/* Ensure icons are visible */
i[class*="fa-"] {
    display: inline-block;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    line-height: 1;
}
</style>

<!-- Structured Data -->


<!-- Performance optimization -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">

@yield('css')