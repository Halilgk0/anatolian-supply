<footer class="border-t border-sand-100/10 bg-olive-950 pt-14 pb-[calc(2.5rem+env(safe-area-inset-bottom))] text-sand-100/75">
    <div class="container-x grid gap-10 md:grid-cols-[1.4fr_1fr_1fr]">
        <div class="flex flex-col gap-4">
            <img src="{{ asset('images/logo-sand.png') }}" alt="{{ config('app.name') }}" width="1400" height="416" loading="lazy" class="h-auto w-48">
            <p class="max-w-xs text-sm leading-relaxed">Saha giyimi ve taktik ekipman. Sitede satış yapılmıyor; sipariş ve fiyat için Instagram’dan ya da e-postayla yazabilirsin.</p>
        </div>

        <nav aria-label="Alt menü" class="flex flex-col gap-3 text-[0.95rem]">
            <p class="font-semibold text-sand-100">Site</p>
            <a href="{{ route('products.index') }}" class="hover:text-sand-50">Ürünler</a>
            <a href="{{ route('home') }}#hikaye" class="hover:text-sand-50">Hikâye</a>
            <a href="{{ route('home') }}#siparis" class="hover:text-sand-50">Nasıl sipariş verilir</a>
        </nav>

        <div class="flex flex-col gap-3 text-[0.95rem]">
            <p class="font-semibold text-sand-100">İletişim</p>
            <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 hover:text-sand-50">
                <x-icons.instagram class="size-5" />
                {{ '@'.config('store.instagram_handle') }}
            </a>
            <a href="mailto:{{ config('store.inquiry_email') }}" class="inline-flex items-center gap-2 break-all hover:text-sand-50">
                <x-icons.mail class="size-5 shrink-0" />
                {{ config('store.inquiry_email') }}
            </a>
        </div>
    </div>

    <div class="container-x mt-12 flex flex-col gap-2 text-xs text-sand-100/50 sm:flex-row sm:items-center sm:justify-between">
        <p>© {{ now()->year }} {{ config('app.name') }}</p>
        <p class="inline-flex items-center gap-2">
            <x-crescent class="h-3 w-auto text-signal" />
            Anadolu’dan
        </p>
    </div>
</footer>
