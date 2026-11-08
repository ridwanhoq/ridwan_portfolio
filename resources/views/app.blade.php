<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="color-scheme" content="light dark">

        {{-- Apply the saved or system theme before first paint to avoid a flash. --}}
        <script>
            (function () {
                try {
                    var stored = localStorage.getItem('appearance');
                    var appearance = stored || '{{ $appearance ?? "system" }}';
                    var dark = appearance === 'dark' || (appearance === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    document.documentElement.classList.toggle('dark', dark);
                } catch (e) {}
            })();
        </script>

        <style>
            html { background-color: #ffffff; }
            html.dark { background-color: #111827; }
        </style>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Meta tags. With SSR enabled, Inertia renders these from <SeoHead> instead of this fallback slot. --}}
        <x-inertia::head>
            @isset($seo)
                <title data-inertia="">{{ $seo['title'] }}</title>
                @if ($seo['description'])
                    <meta name="description" content="{{ $seo['description'] }}" data-inertia="description">
                @endif
                <meta name="robots" content="{{ $seo['robots'] }}" data-inertia="robots">
                <link rel="canonical" href="{{ $seo['canonical'] }}" data-inertia="canonical">
                <meta property="og:type" content="{{ $seo['type'] }}" data-inertia="og:type">
                <meta property="og:site_name" content="{{ $seo['site_name'] }}" data-inertia="og:site_name">
                <meta property="og:title" content="{{ $seo['title'] }}" data-inertia="og:title">
                @if ($seo['description'])
                    <meta property="og:description" content="{{ $seo['description'] }}" data-inertia="og:description">
                @endif
                <meta property="og:url" content="{{ $seo['canonical'] }}" data-inertia="og:url">
                <meta property="og:image" content="{{ $seo['image'] }}" data-inertia="og:image">
                <meta property="og:locale" content="en_US" data-inertia="og:locale">
                <meta name="twitter:card" content="summary_large_image" data-inertia="twitter:card">
                <meta name="twitter:title" content="{{ $seo['title'] }}" data-inertia="twitter:title">
                @if ($seo['description'])
                    <meta name="twitter:description" content="{{ $seo['description'] }}" data-inertia="twitter:description">
                @endif
                <meta name="twitter:image" content="{{ $seo['image'] }}" data-inertia="twitter:image">
            @else
                <title data-inertia="">{{ config('app.name') }}</title>
            @endisset
        </x-inertia::head>

        @isset($seo)
            @if (! empty($seo['google_site_verification']))
                <meta name="google-site-verification" content="{{ $seo['google_site_verification'] }}">
            @endif
            @foreach ($seo['json_ld'] as $schema)
                <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
            @endforeach
        @else
            <meta name="robots" content="noindex, nofollow">
        @endisset

        <link rel="sitemap" type="application/xml" href="/sitemap.xml">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
