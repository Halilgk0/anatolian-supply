@props(['product', 'loading' => 'lazy'])

@php
    $mainImage = $product->mainImage();
    $firstColor = $product->colors[0] ?? null;
@endphp

@if ($mainImage)
    {{-- Photos: cut-out PNGs sit straight on the surface, other photos look like a taped print --}}
    <div data-photo @if ($mainImage['cutout']) data-cutout @endif {{ $attributes->merge(['class' => 'product-photo w-full']) }}>
        <span class="print">
            <img src="{{ \App\Models\Product::mediaUrl($mainImage['path']) }}" alt="{{ $product->name }}" loading="{{ $loading }}" decoding="async">
            <span class="tape" aria-hidden="true"></span>
        </span>
    </div>
@elseif ($product->hasIllustration())
    @php
        $palette = \App\Models\Product::palette($firstColor ?? ['hex' => '#5f6744']);
    @endphp
    <div
        data-art
        role="img"
        aria-label="{{ $product->name }} çizimi{{ $firstColor ? ', '.$firstColor['name'].' renk' : '' }}"
        {{ $attributes->merge(['class' => 'product-art mx-auto aspect-[400/440]']) }}
        style="--art-main: {{ $palette['main'] }}; --art-shade: {{ $palette['shade'] }}; --art-detail: {{ $palette['detail'] }};"
    >
        <x-dynamic-component :component="'art.'.$product->illustration" class="size-full" />
    </div>
@else
    <div {{ $attributes->merge(['class' => 'grid w-full place-items-center rounded border-2 border-dashed border-ink/20 text-sm text-ink/50']) }}>Görsel yok</div>
@endif
