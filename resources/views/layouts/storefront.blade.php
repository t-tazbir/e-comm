<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $metaDescription ?? 'Discover everyday essentials and thoughtful finds at Soumik.' }}">
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    @if (isset($ogTitle))
        <meta property="og:type" content="product">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $metaDescription }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
    @endif
    <meta name="theme-color" content="#f8f6f1">
    <title>{{ $title ?? 'Soumik | Thoughtful finds for everyday' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.header')
    @yield('content')
    @include('partials.footer')
</body>
</html>
