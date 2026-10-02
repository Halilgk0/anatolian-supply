@props(['product'])

@php
    $mainImage = $product->mainImage();
@endphp

<div {{ $attributes->merge(['class' => 'grid shrink-0 place-items-center overflow-hidden rounded bg-sand-200']) }}>
    @if ($mainImage)
        <img src="{{ \App\Models\Product::mediaUrl($mainImage['path']) }}" alt="" loading="lazy" class="size-full {{ $mainImage['cutout'] ? 'object-contain p-1' : 'object-cover' }}">
    @else
        <x-product-visual :product="$product" class="h-[85%]" />
    @endif
</div>
