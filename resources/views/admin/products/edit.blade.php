<x-layouts.admin :title="$product->name">
    <a href="{{ route('admin.products.index') }}" class="text-sm text-ink/70 underline-offset-4 hover:underline">Ürünlere dön</a>
    <div class="mt-3 mb-8 flex flex-wrap items-end justify-between gap-4">
        <h1 class="display text-[clamp(3rem,8vw,5rem)]">{{ $product->name }}</h1>
        @if ($product->is_published)
            <a href="{{ $product->url() }}" target="_blank" rel="noopener" class="btn btn-line-dark min-h-11">Sitede gör</a>
        @endif
    </div>

    @include('admin.products.form', [
        'action' => route('admin.products.update', $product),
        'method' => 'PUT',
        'submitLabel' => 'Değişiklikleri kaydet',
    ])

    <section class="mt-12 rounded-lg border border-signal/30 p-5 sm:p-7">
        <h2 class="display text-3xl">Ürünü sil</h2>
        <p class="mt-2 max-w-prose text-[0.95rem] text-ink/70">Ürün ve fotoğrafları kalıcı olarak silinir. Sadece geçici olarak gizlemek istiyorsan “Sitede yayınla” seçeneğini kapatman yeterli.</p>
        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" data-confirm="“{{ $product->name }}” silinsin mi? Fotoğrafları da silinir ve bu işlem geri alınamaz." class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn min-h-11 border-[1.5px] border-signal/60 px-5 text-signal hover:border-signal hover:bg-signal/5">Ürünü kalıcı olarak sil</button>
        </form>
    </section>
</x-layouts.admin>
