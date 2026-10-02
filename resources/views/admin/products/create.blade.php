<x-layouts.admin title="Yeni ürün">
    <a href="{{ route('admin.products.index') }}" class="text-sm text-ink/70 underline-offset-4 hover:underline">Ürünlere dön</a>
    <h1 class="display mt-3 mb-8 text-[clamp(3rem,8vw,5rem)]">Yeni ürün</h1>

    @include('admin.products.form', [
        'action' => route('admin.products.store'),
        'method' => 'POST',
        'submitLabel' => 'Ürünü ekle',
    ])
</x-layouts.admin>
