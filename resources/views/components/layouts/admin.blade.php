@props(['title'])

<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="robots" content="noindex, nofollow">
        <meta name="referrer" content="no-referrer">

        <title>{{ $title }} | Ürün yönetimi</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/admin.js'])
    </head>
    <body class="grain min-h-svh bg-sand-100 font-sans text-ink antialiased">
        <header class="sticky top-0 z-30 bg-olive-950 pt-[env(safe-area-inset-top)] text-sand-100">
            <div class="container-x flex h-16 items-center justify-between gap-4">
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-sand.png') }}" alt="{{ config('app.name') }}" width="1400" height="416" class="h-8 w-auto">
                    <span class="hidden border-l border-sand-100/20 pl-3 text-sm text-sand-100/70 sm:inline">Ürün yönetimi</span>
                </a>
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-line-light min-h-10 px-4 text-sm">Siteyi aç</a>
            </div>
        </header>

        <main class="container-x pt-8 pb-16 sm:pt-12">
            @if (session('status'))
                <div role="status" class="mb-8 flex items-center gap-3 rounded-md bg-olive-800 px-4 py-3 text-sand-50">
                    <svg viewBox="0 0 24 24" class="size-5 shrink-0 text-coyote-300" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" /></svg>
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </body>
</html>
