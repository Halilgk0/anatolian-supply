<article {{ $attributes->merge(['class' => 'tag grain flex flex-col gap-4 bg-coyote-300 px-6 pt-14 pb-6']) }}>
    <x-icons.instagram class="size-14 text-olive-950" />
    <h3 class="display text-5xl">Yenileri Instagram’da</h3>
    <p class="leading-snug text-ink/80">Yeni gelen ürünleri, stok durumunu ve kombinleri önce Instagram’da paylaşıyoruz. Soru sormak için mesaj atman yeterli.</p>
    <a href="{{ config('store.instagram_url') }}" target="_blank" rel="noopener" class="btn btn-olive mt-auto">
        <x-icons.instagram class="size-5" />
        {{ '@'.config('store.instagram_handle') }}
    </a>
</article>
