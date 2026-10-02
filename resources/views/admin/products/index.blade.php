<x-layouts.admin title="Ürünler">
    <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-5">
        <div>
            <h1 class="display text-[clamp(3.25rem,9vw,5.5rem)]">Ürünler</h1>
            <p class="mt-3 text-ink/70">
                {{ $products->count() }} ürün kayıtlı, {{ $products->where('is_published', true)->count() }} tanesi sitede görünüyor.
            </p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-olive min-h-14 w-full px-7 text-base sm:w-auto">
            <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
            Yeni ürün ekle
        </a>
    </div>

    @if ($products->isEmpty())
        <div class="mt-12 rounded-lg border-2 border-dashed border-ink/20 px-6 py-14 text-center">
            <p class="display text-4xl">Henüz ürün yok</p>
            <p class="mx-auto mt-3 max-w-md text-ink/70">İlk ürününü eklemek için “Yeni ürün ekle” düğmesine bas. Fotoğraf, ad ve kısa açıklama yeterli; gerisini sonra da doldurabilirsin.</p>
        </div>
    @else
        <ul class="mt-10 grid gap-3">
            @foreach ($products as $product)
                <li class="flex flex-col gap-4 rounded-lg border border-ink/10 bg-sand-50/70 p-4 sm:flex-row sm:items-center sm:gap-6 sm:p-5">
                    <div class="flex min-w-0 flex-1 items-center gap-4 sm:gap-5">
                        <x-admin.thumbnail :product="$product" class="size-20 sm:size-24" />

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-sm">
                                <span class="rounded-sm border-[1.5px] border-ink px-1.5 font-display text-base leading-tight font-bold tracking-wide">{{ $product->code }}</span>
                                @if ($product->is_published)
                                    <span class="rounded-full bg-olive-600 px-2.5 py-0.5 text-xs font-semibold text-sand-50">Yayında</span>
                                @else
                                    <span class="rounded-full bg-ink/15 px-2.5 py-0.5 text-xs font-semibold">Taslak, sitede görünmüyor</span>
                                @endif
                            </div>
                            <h2 class="display mt-1.5 truncate text-3xl sm:text-4xl">{{ $product->name }}</h2>
                            <p class="mt-1 text-sm text-ink/70">{{ $product->category }}, sıra {{ $product->sort_order }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 sm:shrink-0">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-olive min-h-11 flex-1 px-5 sm:flex-none">Düzenle</a>
                        @if ($product->is_published)
                            <a href="{{ $product->url() }}" target="_blank" rel="noopener" class="btn btn-line-dark min-h-11 flex-1 px-4 sm:flex-none">Sitede gör</a>
                        @endif
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="“{{ $product->name }}” silinsin mi? Fotoğrafları da silinir ve bu işlem geri alınamaz.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn min-h-11 border-[1.5px] border-signal/50 px-4 text-signal hover:border-signal hover:bg-signal/5">Sil</button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</x-layouts.admin>
