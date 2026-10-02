@props(['product'])

<article {{ $attributes->merge(['class' => 'tag grain flex flex-col gap-3 px-6 pt-14 pb-6']) }}>
    <div class="flex items-baseline justify-between gap-4">
        <span class="font-display text-xl font-bold tracking-wide">{{ $product->code }}</span>
        <span class="text-sm text-ink/70">{{ $product->category }}</span>
    </div>

    <a href="{{ $product->url() }}" tabindex="-1" aria-hidden="true" class="block">
        <x-product-visual :product="$product" class="h-[clamp(8rem,21svh,14rem)]" />
    </a>

    <h3 class="display text-[2.5rem] sm:text-5xl">
        <a href="{{ $product->url() }}" class="hover:text-olive-700">{{ $product->name }}</a>
    </h3>
    <p class="line-clamp-3 text-[0.95rem] leading-snug text-ink/80">{{ $product->tagline }}</p>

    <div class="mt-auto flex gap-2 pt-2">
        <a href="{{ $product->url() }}" class="btn btn-olive flex-1">Ürünü incele</a>
        <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-line-dark px-3.5" aria-label="{{ $product->name }} için Instagram’dan yaz">
            <x-icons.instagram class="size-5" />
        </a>
    </div>
</article>
