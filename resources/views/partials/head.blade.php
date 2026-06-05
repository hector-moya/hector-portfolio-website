@php
    $seoSiteName = \App\Support\Seo::siteName();
    $seoTitle = $title ?? $seoSiteName;
    $seoDescription = $description ?? \App\Support\Seo::siteDescription();
    $seoCanonical = $canonical ?? url()->current();
    $seoOgType = $ogType ?? 'website';
    $seoOgImage = $ogImage ?? null;
@endphp
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $seoTitle }}</title>

@if ($seoDescription)
    <meta name="description" content="{{ $seoDescription }}" />
@endif

<link rel="canonical" href="{{ $seoCanonical }}" />

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seoOgType }}" />
<meta property="og:site_name" content="{{ $seoSiteName }}" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:url" content="{{ $seoCanonical }}" />
@if ($seoDescription)
    <meta property="og:description" content="{{ $seoDescription }}" />
@endif
@if ($seoOgImage)
    <meta property="og:image" content="{{ $seoOgImage }}" />
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $seoOgImage ? 'summary_large_image' : 'summary' }}" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
@if ($seoDescription)
    <meta name="twitter:description" content="{{ $seoDescription }}" />
@endif
@if ($seoOgImage)
    <meta name="twitter:image" content="{{ $seoOgImage }}" />
@endif

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">
<meta name="theme-color" content="#0b1410" />

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=inter:400,500,600|cinzel:400,600,900|space-grotesk:300,400,500,600&display=swap" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
