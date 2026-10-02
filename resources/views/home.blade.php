@php
    $starRandomizer = new \Random\Randomizer(new \Random\Engine\Mt19937(1923));
    $stars = collect(range(1, 70))->map(fn () => [
        'x' => $starRandomizer->getInt(0, 1000),
        'y' => $starRandomizer->getInt(0, 520),
        'r' => $starRandomizer->getInt(6, 20) / 10,
        'o' => $starRandomizer->getInt(25, 90) / 100,
    ]);

    $bandWords = ['Saha giyimi', 'Taktik ekipman', 'Anadolu’dan', 'Sipariş için DM'];

    $steps = [
        ['title' => 'Ürünü seç', 'text' => 'Renk ve bedenine ürün sayfasında karar ver. Kumaş, ölçü ve bakım bilgileri de orada.'],
        ['title' => 'Bize yaz', 'text' => 'Instagram’dan mesaj at ya da ürün sayfasındaki formla e-posta gönder. Stok ve fiyatı sana iletelim.'],
        ['title' => 'Kargoya verelim', 'text' => 'Ödeme ve adres bilgisini yazışarak netleştirelim, siparişini kargoya teslim edelim.'],
    ];
@endphp

<x-layouts.app over-hero>
    {{-- Hero: dusk over the steppe; layers drift at different speeds while scrolling --}}
    <section data-hero class="relative h-svh min-h-[36rem] overflow-hidden bg-[linear-gradient(to_bottom,#151910_0%,#222919_42%,#3d4529_78%,#4f5634_100%)]">
        <div class="pointer-events-none absolute inset-x-0 bottom-[8%] h-[55%] bg-[radial-gradient(ellipse_at_50%_100%,rgb(194_165_119/0.38),transparent_65%)]" aria-hidden="true"></div>

        <div data-parallax="480" class="pointer-events-none absolute inset-x-0 top-0 h-[70%]" aria-hidden="true">
            <svg viewBox="0 0 1000 520" preserveAspectRatio="xMidYMid slice" class="size-full">
                @foreach ($stars as $star)
                    <circle cx="{{ $star['x'] }}" cy="{{ $star['y'] }}" r="{{ $star['r'] }}" fill="#f3eddd" opacity="{{ $star['o'] }}" />
                @endforeach
            </svg>
        </div>

        <div data-parallax="560" data-parallax-x="-90" class="pointer-events-none absolute top-[13%] right-[9%] w-[clamp(5rem,12vw,9rem)] text-sand-50 sm:top-[15%] sm:right-[12%]" aria-hidden="true">
            <div class="absolute top-1/2 left-1/2 aspect-square w-[340%] -translate-1/2 rounded-full bg-[radial-gradient(closest-side,rgb(243_237_221/0.2),transparent)]"></div>
            <x-crescent class="relative w-full drop-shadow-[0_0_18px_rgb(243_237_221/0.45)]" />
        </div>

        <svg data-parallax="330" viewBox="0 0 1440 320" preserveAspectRatio="xMidYMax slice" class="pointer-events-none absolute inset-x-0 bottom-0 h-[50%] w-full" aria-hidden="true">
            <path fill="#4a5236" d="M0,190 L80,170 L150,182 L230,140 L300,160 L360,120 L420,150 L500,110 L560,135 L640,95 L700,128 L780,105 L860,140 L930,100 L1010,130 L1080,92 L1150,125 L1220,110 L1300,145 L1370,125 L1440,150 L1440,320 L0,320 Z" />
        </svg>

        <svg data-parallax="170" viewBox="0 0 1440 320" preserveAspectRatio="xMidYMax slice" class="pointer-events-none absolute inset-x-0 bottom-0 h-[42%] w-full" aria-hidden="true">
            <path fill="#363d27" d="M0,240 L120,215 L200,228 L290,190 L360,205 L440,180 L520,210 L600,195 L700,215 L780,190 L860,150 L920,110 L960,82 L985,70 L1010,80 L1050,108 L1110,150 L1180,185 L1260,200 L1340,190 L1440,210 L1440,320 L0,320 Z" />
            <path fill="#e4dabe" opacity=".85" d="M920,110 L960,82 L985,70 L1010,80 L1050,108 L1030,104 L1012,114 L995,100 L978,116 L960,104 L944,118 Z" />
        </svg>

        <div data-parallax="380" data-fade="1.5" class="relative z-10 flex h-full flex-col items-center justify-center gap-7 pb-[18svh] text-center container-x">
            <h1 class="w-[min(90vw,50rem)]">
                <img src="{{ asset('images/logo-sand.png') }}" alt="{{ config('app.name') }}" width="1400" height="416" fetchpriority="high" class="h-auto w-full">
            </h1>
            <p class="max-w-[34ch] text-lg leading-snug text-sand-100/85 sm:text-xl">Anadolu’nun dağlarından ve bozkırından ilham alan saha giyimi ve taktik ekipman.</p>
            <div class="flex w-full max-w-md flex-col gap-3 sm:w-auto sm:max-w-none sm:flex-row">
                <a href="#urunler" class="btn btn-sand min-h-13 px-7 text-base">Ürünleri incele</a>
                <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-line-light min-h-13 px-7 text-base">
                    <x-icons.instagram class="size-5" />
                    Instagram’da takip et
                </a>
            </div>
        </div>

        <svg viewBox="0 0 1440 320" preserveAspectRatio="xMidYMax slice" class="pointer-events-none absolute inset-x-0 -bottom-px z-20 h-[22%] w-full" aria-hidden="true">
            <path fill="#262c1c" d="M0,280 C120,250 220,262 320,270 C430,280 520,240 640,245 C760,250 830,280 960,275 C1080,270 1160,240 1280,250 C1360,256 1410,268 1440,262 L1440,320 L0,320 Z" />
        </svg>
    </section>

    {{-- Crossed bands that run with the scroll direction --}}
    <div class="relative z-30 -mt-10 overflow-hidden bg-olive-900 pt-2 pb-10 sm:-mt-14" aria-hidden="true">
        <div class="-mx-4 -rotate-2 bg-coyote-400 py-2.5 text-olive-950 shadow-[0_10px_30px_-12px_rgb(0_0_0/0.6)]">
            <div data-marquee="1" class="marquee-row">
                @for ($copy = 0; $copy < 4; $copy++)
                    <div class="flex shrink-0 items-center">
                        @foreach ($bandWords as $word)
                            <span class="display px-5 text-[clamp(2rem,6vw,4.25rem)] leading-none whitespace-nowrap">{{ $word }}</span>
                            <x-crescent class="h-[clamp(1rem,2.4vw,1.6rem)] w-auto shrink-0 text-signal" />
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
        <div class="-mx-4 -mt-1 rotate-[1.5deg] bg-olive-700 py-2 text-sand-200">
            <div data-marquee="-1" class="marquee-row">
                @for ($copy = 0; $copy < 4; $copy++)
                    <div class="flex shrink-0 items-center">
                        @foreach ($products as $product)
                            <span class="display px-5 text-[clamp(1.35rem,3.4vw,2.4rem)] leading-none whitespace-nowrap">{{ $product->name }}</span>
                            <span class="font-display text-[clamp(1rem,2vw,1.4rem)] font-bold text-coyote-300">{{ $product->code }}</span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Product rail: tags hang from a rod and slide sideways as the page scrolls --}}
    <section id="urunler" data-rail aria-labelledby="urunler-baslik" class="relative bg-olive-900">
        <div data-rail-sticky class="flex flex-col overflow-hidden pt-[clamp(1.25rem,7svh,5rem)] pb-4">
            <div class="container-x flex flex-wrap items-end justify-between gap-x-10 gap-y-3">
                <h2 id="urunler-baslik" class="display text-[clamp(3.5rem,11vw,7.5rem)] text-sand-100">Ürünler</h2>
                <p class="max-w-sm text-sand-100/75 max-sm:hidden [@media(height<760px)]:hidden">Renk, beden ve kumaş bilgisi her ürünün sayfasında. Fiyat ve sipariş için Instagram’dan yazabilirsin.</p>
                <div class="flex w-full items-center gap-4 text-sm text-sand-100/70">
                    <span class="font-display text-lg font-bold text-sand-100" aria-hidden="true"><span data-rail-count>1</span> / {{ $products->count() + 2 }}</span>
                    <span class="relative h-0.5 flex-1 overflow-hidden rounded bg-sand-100/15" aria-hidden="true">
                        <span data-rail-bar class="absolute inset-0 origin-left scale-x-0 bg-coyote-300"></span>
                    </span>
                    <a href="{{ route('products.index') }}" class="btn btn-line-light min-h-10 shrink-0 px-4 text-sm">Tüm ürünler</a>
                </div>
            </div>

            <div class="relative mt-[clamp(1rem,4svh,2.5rem)] flex-1">
                <div class="rod absolute inset-x-0 top-0 z-10"></div>
                <ul data-rail-track role="list" class="relative flex items-start gap-[clamp(1.5rem,5vw,4.5rem)] px-[max(1rem,calc((100vw-80rem)/2+2.5rem))] pt-1 pb-10">
                    @foreach ($products as $product)
                        <li data-hanger class="hanger">
                            <x-product-tag :product="$product" class="w-[min(80vw,23rem)]" />
                        </li>
                    @endforeach

                    <li data-hanger class="hanger">
                        <article class="tag grain flex w-[min(80vw,23rem)] flex-col gap-4 px-6 pt-14 pb-6">
                            <svg viewBox="0 0 24 24" class="size-14 text-olive-800" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1" /><rect x="13.5" y="3.5" width="7" height="7" rx="1" /><rect x="3.5" y="13.5" width="7" height="7" rx="1" /><rect x="13.5" y="13.5" width="7" height="7" rx="1" /></svg>
                            <h3 class="display text-5xl">Tüm ürünler</h3>
                            <p class="leading-snug text-ink/80">{{ $totalCount }} ürünün hepsi tek sayfada. Dış giyim, ekipman ya da aksesuar diye kategoriye göre de bakabilirsin.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-olive mt-auto">Tüm ürünleri gör</a>
                        </article>
                    </li>

                    <li data-hanger class="hanger">
                        <x-instagram-tag class="w-[min(80vw,23rem)]" />
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- Story on a topographic sheet --}}
    <section id="hikaye" aria-labelledby="hikaye-baslik" class="grain relative overflow-hidden bg-sand-100 py-[clamp(5rem,14vw,10rem)] text-ink">
        <div data-parallax="-90" data-rotate="9" class="pointer-events-none absolute -inset-[12%] text-coyote-500/45" aria-hidden="true">
            <svg data-contours="640,400" viewBox="0 0 1000 800" preserveAspectRatio="xMidYMid slice" class="size-full">
                <g transform="translate(640 400)" fill="currentColor">
                    <path d="M0,-10 L9,7 L-9,7 Z" />
                    <text x="16" y="6" font-size="17" font-weight="600" font-family="var(--font-archivo), sans-serif">Erciyes 3917 m</text>
                </g>
            </svg>
        </div>

        <div class="relative container-x">
            <h2 id="hikaye-baslik" class="display text-[clamp(3.25rem,10vw,7rem)]">Bozkırdan zirveye</h2>
            <p data-reveal-words class="mt-8 max-w-[24ch] text-[clamp(1.65rem,4.4vw,3.4rem)] leading-[1.1] font-medium tracking-[-0.01em]">
                Anadolu’nun dört mevsimine ve engebeli arazisine uygun giyim ve ekipmanı tek çatı altında topluyoruz. Kumaşından dikişine kadar sahada işe yarayanı seçiyor, gerisini rafa koymuyoruz.
            </p>
        </div>
    </section>

    {{-- Ordering steps along a route line that fills while scrolling --}}
    <section id="siparis" data-route aria-labelledby="siparis-baslik" class="relative bg-sand-200 pt-[clamp(4rem,10vw,7rem)] pb-[clamp(8rem,18vw,13rem)] text-ink">
        <div class="container-x">
            <h2 id="siparis-baslik" class="display max-w-[12ch] text-[clamp(3rem,8vw,6rem)]">Nasıl sipariş verilir?</h2>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-ink/80">Sitede sepet ya da ödeme yok. Siparişleri Instagram ve e-posta üzerinden, birebir yazışarak alıyoruz.</p>

            <div class="relative mt-14">
                <div data-route-line class="absolute top-6 bottom-6 left-[calc(1.5rem-1px)] w-0.5 bg-ink/15 md:top-[calc(1.5rem-1px)] md:right-[calc((100%-4rem)/6)] md:bottom-auto md:left-[calc((100%-4rem)/6)] md:h-0.5 md:w-auto" aria-hidden="true">
                    <div class="route-fill absolute inset-0 bg-coyote-500"></div>
                </div>

                <ol class="relative grid gap-12 md:grid-cols-3 md:gap-8">
                    @foreach ($steps as $step)
                        <li data-step class="group relative grid grid-cols-[3rem_1fr] content-start gap-5 md:grid-cols-1 md:justify-items-center md:text-center">
                            <span data-step-marker class="display relative z-10 grid size-12 place-items-center rounded-full border-2 border-ink/25 bg-sand-200 text-2xl transition duration-300 group-data-reached:scale-110 group-data-reached:border-coyote-500 group-data-reached:bg-coyote-400 group-data-reached:text-olive-950">{{ $loop->iteration }}</span>
                            <div class="max-w-xs">
                                <h3 class="display text-[2.1rem]">{{ $step['title'] }}</h3>
                                <p class="mt-2 leading-relaxed text-ink/80">{{ $step['text'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <svg viewBox="0 0 1440 160" preserveAspectRatio="xMidYMax slice" class="pointer-events-none absolute inset-x-0 -bottom-px h-[clamp(4rem,10vw,9rem)] w-full" aria-hidden="true">
            <path fill="#1d2215" d="M0,120 L110,96 L190,108 L300,70 L380,92 L470,60 L540,84 L640,40 L700,62 L760,48 L860,90 L950,72 L1040,100 L1130,64 L1220,88 L1320,70 L1440,96 L1440,160 L0,160 Z" />
        </svg>
    </section>

    {{-- Instagram call to action under a rising moon --}}
    <section aria-labelledby="instagram-baslik" class="relative overflow-hidden bg-olive-950 pt-[clamp(5rem,12vw,9rem)] pb-[clamp(5rem,12vw,9rem)] text-center">
        <div data-parallax="-140" class="pointer-events-none absolute top-[5%] right-[8%] w-[clamp(3.5rem,8vw,6rem)] text-sand-50/90 sm:right-[12%]" aria-hidden="true">
            <div class="absolute top-1/2 left-1/2 aspect-square w-[340%] -translate-1/2 rounded-full bg-[radial-gradient(closest-side,rgb(243_237_221/0.14),transparent)]"></div>
            <x-crescent class="relative w-full" />
        </div>

        <div class="relative container-x flex flex-col items-center gap-6">
            <x-icons.instagram class="size-12 text-coyote-300" />
            <h2 id="instagram-baslik" class="display max-w-full text-[clamp(2.4rem,12.5vw,8.5rem)] break-words text-sand-50 normal-case">{{ '@'.config('store.instagram_handle') }}</h2>
            <p class="max-w-[38ch] text-lg leading-snug text-sand-100/80">Yeni ürünler, stok durumu ve siparişler için Instagram’dayız. Sorunu mesajla yazman yeterli.</p>
            <div class="mt-2 flex w-full max-w-md flex-col gap-3 sm:w-auto sm:max-w-none sm:flex-row">
                <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-sand min-h-13 px-7 text-base">
                    <x-icons.instagram class="size-5" />
                    Instagram’a git
                </a>
                <a href="mailto:{{ config('store.inquiry_email') }}" class="btn btn-line-light min-h-13 px-7 text-base">
                    <x-icons.mail class="size-5" />
                    E-posta gönder
                </a>
            </div>
        </div>
    </section>
</x-layouts.app>
