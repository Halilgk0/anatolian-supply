<x-layouts.app
    :title="$activeCategory ? $activeCategory['name'] : 'Ürünler'"
    description="Anatolian Supply Co. saha giyimi, taktik ekipman ve aksesuarları. Ürünleri incele, fiyat ve sipariş için Instagram’dan yaz."
>
    {{-- Supply-room pegboard: every product tag hangs from its own peg --}}
    <section class="relative min-h-svh overflow-hidden bg-olive-900 bg-[radial-gradient(circle,rgb(0_0_0/0.32)_1.6px,transparent_2.2px)] bg-[length:30px_30px] pt-[calc(5.5rem+env(safe-area-inset-top))] pb-[clamp(5rem,10vw,8rem)] text-sand-100">
        <div class="container-x">
            <nav aria-label="Sayfa konumu" class="text-sm text-sand-100/65">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="underline-offset-4 hover:text-sand-50 hover:underline">Ana sayfa</a></li>
                    <li aria-hidden="true">/</li>
                    @if ($activeCategory)
                        <li><a href="{{ route('products.index') }}" class="underline-offset-4 hover:text-sand-50 hover:underline">Ürünler</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="text-sand-100">{{ $activeCategory['name'] }}</li>
                    @else
                        <li aria-current="page" class="text-sand-100">Ürünler</li>
                    @endif
                </ol>
            </nav>

            <div class="mt-5 flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <div>
                    <h1 class="display text-[clamp(3.75rem,13vw,9.5rem)]">{{ $activeCategory ? $activeCategory['name'] : 'Ürünler' }}</h1>
                </div>
                <p class="max-w-sm leading-relaxed text-sand-100/75">
                    {{ $products->count() }} ürün. Renk, beden ve kumaş bilgisi ürün sayfalarında; fiyat ve sipariş için Instagram’dan ya da e-postayla yazabilirsin.
                </p>
            </div>

            @if ($categories->count() > 1)
                <nav data-category-nav aria-label="Kategoriler" class="relative -mx-4 mt-8 overflow-x-auto px-4 [scrollbar-width:none]">
                    <ul class="flex w-max gap-2">
                        @foreach ([['name' => 'Tümü', 'slug' => null, 'count' => $totalCount], ...$categories] as $category)
                            @php
                                $isActive = $category['slug'] === ($activeCategory['slug'] ?? null);
                            @endphp
                            <li>
                                <a
                                    href="{{ $category['slug'] ? route('products.index', ['kategori' => $category['slug']]) : route('products.index') }}"
                                    @if ($isActive) aria-current="page" @endif
                                    class="inline-flex min-h-11 items-center gap-2 rounded-full border-[1.5px] border-sand-100/30 px-4 font-medium whitespace-nowrap transition-colors hover:border-sand-100 aria-[current=page]:border-sand-100 aria-[current=page]:bg-sand-100 aria-[current=page]:text-olive-950"
                                >
                                    {{ $category['name'] }}
                                    <span class="text-xs tabular-nums opacity-70">{{ $category['count'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            @if ($products->isEmpty())
                <div class="mt-14 max-w-md">
                    <div class="hanger [--string:3rem]">
                        <article class="tag grain flex flex-col gap-4 px-6 pt-14 pb-6">
                            <h2 class="display text-5xl">Yakında</h2>
                            <p class="leading-snug text-ink/80">Raflara yeni ürünler geliyor. Haberdar olmak için Instagram hesabımızı takip edebilirsin.</p>
                            <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-olive">
                                <x-icons.instagram class="size-5" />
                                Instagram’da takip et
                            </a>
                        </article>
                    </div>
                </div>
            @else
                <ul role="list" class="mt-12 grid gap-x-8 gap-y-12 sm:grid-cols-2 lg:grid-cols-3 lg:gap-x-10">
                    @foreach ($products as $product)
                        <li data-swing class="hanger [--string:3rem]">
                            <x-product-tag :product="$product" class="w-full" />
                        </li>
                    @endforeach

                    <li data-swing class="hanger [--string:3rem]">
                        <x-instagram-tag />
                    </li>
                </ul>
            @endif
        </div>
    </section>
</x-layouts.app>
