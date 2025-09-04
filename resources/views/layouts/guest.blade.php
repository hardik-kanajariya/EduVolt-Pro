<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'EduVault Pro - Complete Digital Education Management Solution')</title>
    <meta name="description" content="@yield('description', 'EduVault Pro is a comprehensive school management system built with Laravel and Filament. Manage students, teachers, attendance, fees, library, and more with our advanced digital solution.')">
    <meta name="keywords" content="@yield('keywords', 'school management system, education software, student management, teacher portal, attendance tracking, fee management, library system, Laravel, Filament')">
    <meta name="author" content="EduVault Pro">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'EduVault Pro - Complete Digital Education Management Solution')">
    <meta property="og:description" content="@yield('og_description', 'Transform your school with our comprehensive digital management system. Built for modern educational institutions.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EduVault Pro">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'EduVault Pro - Complete Digital Education Management Solution')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Transform your school with our comprehensive digital management system.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-image.png'))">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Head Content -->
    @stack('head')
</head>
<body class="font-sans antialiased bg-white">
    <!-- Navigation -->
    @include('components.navbar')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('components.footer')
    
    <!-- Scripts -->
    @stack('scripts')
    
    <!-- Google Analytics (Production Only) -->
    @if(app()->environment('production'))
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    @endif
</body>
</html>
