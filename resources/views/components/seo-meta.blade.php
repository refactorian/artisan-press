@props(['metadata' => []])

@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
    $title = $metadata['title'] ?? $siteName;
    $metaDescription = $metadata['meta_description'] ?? \App\Models\Setting::get('default_seo_desc', '');
    $canonical = $metadata['canonical_url'] ?? url()->current();
    $robots = $metadata['robots'] ?? 'index, follow';
    $og = $metadata['og'] ?? [];
    $twitter = $metadata['twitter'] ?? [];
    $schema = $metadata['schema'] ?? null;
@endphp

<title>{{ $title }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="robots" content="{{ $robots }}">

<!-- Open Graph / Facebook -->
<meta property="og:site_name" content="{{ $og['site_name'] ?? $siteName }}">
<meta property="og:type" content="{{ $og['type'] ?? 'website' }}">
<meta property="og:url" content="{{ $og['url'] ?? $canonical }}">
<meta property="og:title" content="{{ $og['title'] ?? $title }}">
<meta property="og:description" content="{{ $og['description'] ?? $metaDescription }}">
@if(!empty($og['image']))
    <meta property="og:image" content="{{ $og['image'] }}">
@endif

<!-- Twitter -->
<meta name="twitter:card" content="{{ $twitter['card'] ?? 'summary_large_image' }}">
<meta name="twitter:url" content="{{ $og['url'] ?? $canonical }}">
<meta name="twitter:title" content="{{ $twitter['title'] ?? $title }}">
<meta name="twitter:description" content="{{ $twitter['description'] ?? $metaDescription }}">
@if(!empty($twitter['image']))
    <meta name="twitter:image" content="{{ $twitter['image'] }}">
@endif
@if(!empty($twitter['site']))
    <meta name="twitter:site" content="{{ $twitter['site'] }}">
@endif

<!-- RSS & Atom Feeds -->
<link rel="alternate" type="application/rss+xml" title="{{ $siteName }} RSS Feed" href="{{ route('feed.rss') }}">
<link rel="alternate" type="application/atom+xml" title="{{ $siteName }} Atom Feed" href="{{ route('feed.atom') }}">
<link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">

<!-- JSON-LD Structured Data -->
@if(!empty($schema))
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif
