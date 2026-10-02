@php
    $hasSizeChoice = count($product->sizes) > 1;
    $purchasePlatform = $product->purchasePlatform();
    $gallery = collect($product->images)->map(fn (array $image): array => [
        'src' => \App\Models\Product::mediaUrl($image['path']),
        'cutout' => $image['cutout'],
    ]);
@endphp

<x-layouts.app :title="$product->name" :description="$product->tagline" class="max-lg:pb-[calc(5.5rem+env(safe-area-inset-bottom))]">
    <div
        data-product-page
        data-mail-to="{{ config('store.inquiry_email') }}"
        data-mail-subject="{{ $product->inquirySubject() }}"
        data-mail-body="{{ $product->inquiryBody() }}"
    >
        <section class="grain relative bg-sand-100 pt-[calc(5.5rem+env(safe-area-inset-top))] pb-16 text-ink lg:pb-24">
            <div class="container-x">
                <nav aria-label="Sayfa konumu" class="text-sm text-ink/70">
                    <ol class="flex flex-wrap items-center gap-2">
                        <li><a href="{{ route('home') }}" class="underline-offset-4 hover:underline">Ana sayfa</a></li>
                        <li aria-hidden="true">/</li>
                        <li><a href="{{ route('products.index') }}" class="underline-offset-4 hover:underline">Ürünler</a></li>
                        <li aria-hidden="true">/</li>
                        <li aria-current="page" class="text-ink">{{ $product->name }}</li>
                    </ol>
                </nav>

                <div class="mt-6 grid gap-10 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:gap-16">
                    <div class="lg:sticky lg:top-24 lg:self-start">
                        <div data-tilt class="relative grid aspect-[4/4.2] place-items-center overflow-hidden rounded-md bg-sand-200 [perspective:900px]">
                            <span data-parallax="70" class="display pointer-events-none absolute -bottom-2 -left-2 text-[clamp(6rem,24vw,13rem)] text-ink/[0.07]" aria-hidden="true">{{ $product->code }}</span>
                            <div data-parallax="-30" data-rotate="-5" class="relative grid size-[86%] place-items-center">
                                <x-product-visual :product="$product" loading="eager" data-tilt-target class="h-full transition-transform duration-300 ease-out" />
                            </div>
                        </div>

                        @if ($gallery->count() > 1)
                            <div class="mt-4 flex flex-wrap gap-3" aria-label="Ürün fotoğrafları">
                                @foreach ($gallery as $image)
                                    <button
                                        type="button"
                                        data-gallery-thumb
                                        data-src="{{ $image['src'] }}"
                                        @if ($image['cutout']) data-cutout @endif
                                        aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                        class="size-16 overflow-hidden rounded border-2 border-transparent bg-sand-200 p-1 transition-colors hover:border-ink/40 aria-pressed:border-ink sm:size-20"
                                    >
                                        <span class="sr-only">Fotoğraf {{ $loop->iteration }}</span>
                                        <img src="{{ $image['src'] }}" alt="" loading="lazy" class="size-full {{ $image['cutout'] ? 'object-contain' : 'object-cover' }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        @if ($product->colors)
                            <fieldset class="mt-5">
                                <legend class="text-sm font-semibold">Renk: <span data-color-label class="font-normal">{{ $product->colors[0]['name'] }}</span></legend>
                                <div class="mt-3 flex flex-wrap gap-4 pl-1">
                                    @foreach ($product->colors as $color)
                                        @php
                                            $palette = \App\Models\Product::palette($color);
                                        @endphp
                                        <label class="cursor-pointer">
                                            <input
                                                type="radio"
                                                name="color-choice"
                                                value="{{ $color['name'] }}"
                                                data-main="{{ $palette['main'] }}"
                                                data-shade="{{ $palette['shade'] }}"
                                                data-detail="{{ $palette['detail'] }}"
                                                @if ($color['image'])
                                                    data-image="{{ \App\Models\Product::mediaUrl($color['image']) }}"
                                                    @if ($color['cutout']) data-cutout @endif
                                                @endif
                                                class="sr-only"
                                                @checked($loop->first)
                                            >
                                            <span class="swatch block" style="--swatch: {{ $color['hex'] }}"></span>
                                            <span class="sr-only">{{ $color['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endif
                    </div>

                    <div class="flex flex-col">
                        <p class="flex flex-wrap items-center gap-3">
                            <span class="rounded-sm border-[1.5px] border-ink px-2 py-0.5 font-display text-lg leading-tight font-bold tracking-wide">{{ $product->code }}</span>
                            <span class="text-sm text-ink/70">{{ $product->category }}</span>
                        </p>
                        <h1 class="display mt-4 text-[clamp(3.25rem,9vw,6.5rem)]">{{ $product->name }}</h1>
                        <p class="mt-5 text-xl leading-snug font-medium">{{ $product->tagline }}</p>
                        <p class="mt-4 max-w-prose leading-relaxed text-ink/80">{{ $product->description }}</p>

                        @if ($hasSizeChoice)
                            <fieldset class="mt-8">
                                <legend class="text-sm font-semibold">Beden</legend>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($product->sizes as $size)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="size-choice" value="{{ $size }}" class="sr-only">
                                            <span class="size-chip">{{ $size }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @elseif ($product->sizes)
                            <p class="mt-8 text-sm"><span class="font-semibold">Beden:</span> {{ $product->sizes[0] }}</p>
                        @endif

                        <div data-primary-cta class="mt-8 grid gap-3">
                            @if ($purchasePlatform)
                                <a href="{{ $product->purchase_url }}" target="_blank" rel="noopener" data-purchase-link class="btn btn-olive min-h-14 text-base">
                                    <x-icons.external class="size-5" />
                                    {{ $purchasePlatform }} üzerinden satın al
                                </a>
                            @endif
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn {{ $purchasePlatform ? 'btn-line-dark' : 'btn-olive' }} min-h-14 flex-1 text-base">
                                    <x-icons.instagram class="size-5" />
                                    Instagram’dan sor
                                </a>
                                <button type="button" data-open-inquiry class="btn btn-line-dark min-h-14 flex-1 text-base">
                                    <x-icons.mail class="size-5" />
                                    E-postayla bilgi al
                                </button>
                            </div>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-ink/70">
                            @if ($purchasePlatform)
                                Ödeme ve kargo {{ $purchasePlatform }} üzerinden yapılır. Ürünle ilgili sorun varsa Instagram’dan mesaj at ya da e-postayla bilgi iste.
                            @else
                                Sitede satış yapılmıyor. Fiyat, stok ve sipariş için Instagram’dan mesaj at ya da e-postayla bilgi iste.
                            @endif
                        </p>

                        @if (session('inquiry_sent'))
                            <p role="status" class="mt-5 rounded-sm border-l-4 border-olive-600 bg-sand-200 px-4 py-3 text-[0.95rem]">{{ session('inquiry_sent') }}</p>
                        @endif

                        @if ($product->features)
                            <div class="mt-12 border-t border-ink/15 pt-8">
                                <h2 class="display text-4xl">Öne çıkanlar</h2>
                                <ul class="mt-5 grid gap-3">
                                    @foreach ($product->features as $feature)
                                        <li class="flex gap-3 leading-snug">
                                            <span class="mt-[0.45em] size-2 shrink-0 bg-coyote-500" aria-hidden="true"></span>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if ($product->specs || $product->colors || $product->sizes)
                            <div class="mt-10 border-t border-ink/15 pt-8">
                                <h2 class="display text-4xl">Teknik bilgiler</h2>
                                <dl class="mt-5 grid grid-cols-[minmax(6rem,auto)_1fr] gap-x-6 gap-y-3 text-[0.95rem]">
                                    @foreach ($product->specs as $spec)
                                        <dt class="font-semibold">{{ $spec['label'] }}</dt>
                                        <dd class="text-ink/80">{{ $spec['value'] }}</dd>
                                    @endforeach
                                    @if ($product->colors)
                                        <dt class="font-semibold">Renkler</dt>
                                        <dd class="text-ink/80">{{ implode(', ', $product->colorNames()) }}</dd>
                                    @endif
                                    @if ($product->sizes)
                                        <dt class="font-semibold">Bedenler</dt>
                                        <dd class="text-ink/80">{{ implode(', ', $product->sizes) }}</dd>
                                    @endif
                                </dl>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        @if ($otherProducts->isNotEmpty())
            <section aria-labelledby="diger-urunler" class="bg-olive-900 py-[clamp(4rem,10vw,7rem)]">
                <div class="container-x">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <h2 id="diger-urunler" class="display text-[clamp(2.75rem,7vw,5rem)] text-sand-100">Diğer ürünler</h2>
                        <a href="{{ route('products.index') }}" class="btn btn-line-light min-h-11">Tüm ürünleri gör</a>
                    </div>
                    <div class="mt-10 flex flex-wrap justify-center gap-8 sm:justify-start">
                        @foreach ($otherProducts as $otherProduct)
                            <x-product-tag :product="$otherProduct" class="w-[min(100%,23rem)]" />
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <div data-mobile-cta inert class="fixed inset-x-0 bottom-0 z-30 flex translate-y-full gap-2 border-t border-ink/10 bg-sand-100/95 px-4 pt-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] text-ink backdrop-blur transition-transform duration-300 lg:hidden">
            @if ($purchasePlatform)
                <a href="{{ $product->purchase_url }}" target="_blank" rel="noopener" aria-label="{{ $purchasePlatform }} üzerinden satın al" class="btn btn-olive flex-1 px-3 whitespace-nowrap">
                    <x-icons.external class="size-5" />
                    Satın al
                </a>
                <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-line-dark flex-1 px-3 whitespace-nowrap">
                    <x-icons.instagram class="size-5" />
                    Instagram
                </a>
            @else
                <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-olive flex-1 px-3 whitespace-nowrap">
                    <x-icons.instagram class="size-5" />
                    Instagram
                </a>
                <button type="button" data-open-inquiry class="btn btn-line-dark flex-1 px-3 whitespace-nowrap">
                    <x-icons.mail class="size-5" />
                    Bilgi al
                </button>
            @endif
        </div>

        <x-inquiry-dialog :product="$product" :size-required="$hasSizeChoice" />
    </div>
</x-layouts.app>
