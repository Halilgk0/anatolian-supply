@props([
    'title' => null,
    'description' => 'Anatolian Supply Co. saha giyimi ve taktik ekipman. Ürünleri incele, sipariş ve fiyat için Instagram’dan yaz.',
    'overHero' => false,
])

<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

        <title>{{ $title ? $title.' | '.config('app.name') : config('app.name').' | Saha giyimi ve taktik ekipman' }}</title>
        <meta name="description" content="{{ $description }}">
        <meta name="theme-color" content="#1d2215">

        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('app.name') }}">
        <meta property="og:title" content="{{ $title ?? config('app.name') }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:image" content="{{ asset('content.png') }}">

        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body {{ $attributes->merge(['class' => 'bg-olive-950 font-sans text-sand-100 antialiased']) }}>
        <a href="#icerik" class="btn btn-sand fixed top-3 left-3 z-50 -translate-y-24 focus:translate-y-0">İçeriğe geç</a>

        <x-site-header :over-hero="$overHero" />

        <main id="icerik">
            {{ $slot }}
        </main>

        <x-site-footer />
    </body>
</html>
