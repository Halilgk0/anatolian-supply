@props(['overHero' => false])

@php
    $links = [
        ['label' => 'Ürünler', 'href' => route('products.index'), 'current' => request()->routeIs('products.*')],
        ['label' => 'Hikâye', 'href' => route('home').'#hikaye', 'current' => false],
        ['label' => 'Nasıl sipariş verilir', 'href' => route('home').'#siparis', 'current' => false],
    ];
@endphp

<header data-site-header @if ($overHero) data-over-hero @else data-solid @endif class="site-header fixed inset-x-0 top-0 z-40 pt-[env(safe-area-inset-top)]">
    <div class="container-x flex h-16 items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="header-logo shrink-0" aria-label="{{ config('app.name') }} ana sayfa">
            <img src="{{ asset('images/logo-sand.png') }}" alt="" width="1400" height="416" class="h-9 w-auto sm:h-10">
        </a>

        <nav aria-label="Ana menü" class="hidden items-center gap-8 text-[0.95rem] font-medium md:flex">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" @if ($link['current']) aria-current="page" @endif class="text-sand-100/80 underline-offset-8 transition-colors hover:text-sand-50 aria-[current=page]:text-sand-50 aria-[current=page]:underline">{{ $link['label'] }}</a>
            @endforeach

            <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-sand min-h-10 px-4">
                <x-icons.instagram class="size-5" />
                Instagram
            </a>
        </nav>

        <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobil-menu" class="-mr-2 grid size-12 place-items-center md:hidden">
            <span class="sr-only">Menüyü aç</span>
            <span class="relative block h-3.5 w-7" aria-hidden="true">
                <span class="menu-bar absolute inset-x-0 top-0 h-0.5 rounded bg-sand-100 transition-transform duration-300"></span>
                <span class="menu-bar absolute inset-x-0 bottom-0 h-0.5 rounded bg-sand-100 transition-transform duration-300"></span>
            </span>
        </button>
    </div>

    <div id="mobil-menu" data-menu hidden class="fixed inset-0 top-[calc(4rem+env(safe-area-inset-top))] z-30 overflow-y-auto bg-olive-950 md:hidden">
        <nav aria-label="Mobil menü" class="container-x flex min-h-full flex-col gap-2 pt-8 pb-[calc(2rem+env(safe-area-inset-bottom))]">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" data-menu-link @if ($link['current']) aria-current="page" @endif class="display border-b border-sand-100/10 py-4 text-5xl text-sand-100 aria-[current=page]:text-coyote-300">{{ $link['label'] }}</a>
            @endforeach

            <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-sand mt-8 min-h-14 text-lg">
                <x-icons.instagram class="size-6" />
                Instagram’da takip et
            </a>
            <a href="mailto:{{ config('store.inquiry_email') }}" class="btn btn-line-light min-h-14 text-lg">
                <x-icons.mail class="size-6" />
                {{ config('store.inquiry_email') }}
            </a>
        </nav>
    </div>
</header>
