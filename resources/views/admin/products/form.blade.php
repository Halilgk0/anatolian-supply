@php
    use App\Models\Product;

    $sizeGroups = [
        'Harf bedenler' => ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL'],
        'Sayı bedenler (pantolon beli, ayakkabı numarası)' => array_map('strval', range(28, 46)),
        'Tek ölçü' => ['Tek beden'],
    ];
    $presetSizes = array_merge(...array_values($sizeGroups));
    $selectedSizes = old('sizes', $product->sizes);
    $customSizes = array_values(array_diff($selectedSizes, $presetSizes));

    $colorPresets = [
        'Zeytin' => '#5f6744', 'Coyote' => '#a08259', 'Siyah' => '#3a3b35', 'Haki' => '#b7a77e',
        'Kurşuni' => '#64686a', 'Ranger yeşili' => '#4e5a45', 'Lacivert' => '#2c3446', 'Kum' => '#d6c7a1',
    ];
    $colorRows = old('colors') ?? collect($product->colors)
        ->map(fn (array $color): array => ['name' => $color['name'], 'hex' => $color['hex'], 'existing_image' => $color['image']])
        ->all();

    $specRows = old('specs') ?? ($product->specs ?: array_fill(0, 3, ['label' => '', 'value' => '']));
    $removedImages = old('remove_images', []);
    $mainImage = old('main_image', $product->images ? 'existing:'.$product->images[0]['path'] : null);
@endphp

@if ($errors->any())
    <div role="alert" class="mb-8 rounded-lg border-l-4 border-signal bg-signal/5 px-5 py-4">
        <p class="font-semibold text-signal">Kaydedilemedi. Kırmızıyla işaretli alanları düzeltip tekrar dene.</p>
        <ul class="mt-2 list-inside list-disc text-sm text-ink/80">
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
        <p class="mt-2 text-sm text-ink/70">Bu sayfada yeni seçtiğin fotoğraflar varsa onları yeniden eklemen gerekiyor.</p>
    </div>
@endif

<form
    method="POST"
    action="{{ $action }}"
    enctype="multipart/form-data"
    data-product-form
    class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-10"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6">
        {{-- 1. Photos --}}
        <x-admin.section number="1" title="Fotoğraflar" hint="Ürünün fotoğraflarını ekle. Telefondan çektiğin fotoğrafları doğrudan seçebilirsin; büyük dosyalar yüklenmeden önce otomatik küçültülür.">
            <div data-image-manager class="grid gap-4">
                <label data-dropzone class="grid cursor-pointer place-items-center gap-2 rounded-md border-2 border-dashed border-ink/25 bg-sand-100 px-6 py-10 text-center transition-colors hover:border-olive-600 hover:bg-sand-200 data-dragging:border-olive-600 data-dragging:bg-sand-200">
                    <svg viewBox="0 0 24 24" class="size-10 text-olive-700" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4m0 0-4.5 4.5M12 4l4.5 4.5" /><path d="M4 15v3a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-3" /></svg>
                    <span class="text-lg font-semibold">Fotoğraf seç ya da buraya sürükle</span>
                    <span class="max-w-md text-sm text-ink/70">JPEG, PNG veya WEBP. En fazla 8 fotoğraf. Birden fazla fotoğrafı aynı anda seçebilirsin.</span>
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" data-image-input class="sr-only">
                </label>
                {{-- Filled by admin.js: whether each new photo has a see-through background, in upload order. --}}
                <div data-image-cutouts hidden></div>

                <p data-image-status role="status" class="text-sm font-medium text-olive-700 empty:hidden"></p>
                <x-admin.error for="images" />
                <x-admin.error for="images.*" />

                <ul data-image-list class="grid grid-cols-2 gap-3 empty:hidden sm:grid-cols-3 xl:grid-cols-4">
                    @foreach ($product->images as $image)
                        <li
                            data-image-card
                            data-src="{{ Product::mediaUrl($image['path']) }}"
                            @if ($image['cutout']) data-cutout @endif
                            class="overflow-hidden rounded-md border border-ink/15 bg-sand-50 transition-opacity has-[[data-remove-image]:checked]:opacity-45"
                        >
                            <img src="{{ Product::mediaUrl($image['path']) }}" alt="" class="aspect-square w-full bg-[conic-gradient(#e9e2cc_25%,#f6f1e4_0_50%,#e9e2cc_0_75%,#f6f1e4_0)] bg-[length:16px_16px] object-contain">
                            <div class="grid gap-1 p-2.5 text-sm">
                                <span class="text-xs text-ink/60">{{ $image['cutout'] ? 'Dekupe PNG' : 'Fotoğraf' }}</span>
                                <label class="flex min-h-9 items-center gap-2">
                                    <input type="radio" name="main_image" value="existing:{{ $image['path'] }}" class="size-4 accent-olive-700" @checked($mainImage === 'existing:'.$image['path'])>
                                    Ana fotoğraf
                                </label>
                                <label class="flex min-h-9 items-center gap-2">
                                    <input type="checkbox" name="remove_images[]" value="{{ $image['path'] }}" data-remove-image class="size-4 accent-signal" @checked(in_array($image['path'], $removedImages, true))>
                                    Sil
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <template data-new-image-template>
                    <li data-image-card data-new class="overflow-hidden rounded-md border border-olive-600/40 bg-sand-50">
                        <img src="" alt="" class="aspect-square w-full bg-[conic-gradient(#e9e2cc_25%,#f6f1e4_0_50%,#e9e2cc_0_75%,#f6f1e4_0)] bg-[length:16px_16px] object-contain">
                        <div class="grid gap-1 p-2.5 text-sm">
                            <span data-kind class="text-xs text-ink/60">Yeni</span>
                            <label class="flex min-h-9 items-center gap-2">
                                <input type="radio" name="main_image" value="" class="size-4 accent-olive-700">
                                Ana fotoğraf
                            </label>
                            <button type="button" data-remove-new class="flex min-h-9 items-center gap-2 text-left text-signal">
                                <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
                                Vazgeç
                            </button>
                        </div>
                    </li>
                </template>

                @if ($product->hasIllustration() && ! $product->images)
                    <p class="rounded-md bg-sand-200 px-4 py-3 text-sm leading-relaxed">Bu ürün şu an sitede çizimle gösteriliyor. Fotoğraf eklersen çizimin yerine fotoğraf görünür.</p>
                @endif

                <details class="rounded-md bg-sand-200/70 px-4 py-3 text-sm leading-relaxed">
                    <summary class="cursor-pointer font-semibold">Fotoğraflar sitede nasıl görünür?</summary>
                    <ul class="mt-2 grid list-inside list-disc gap-1 text-ink/80">
                        <li><strong>Arka planı şeffaf PNG (dekupe):</strong> ürün doğrudan zemine oturur, gölgesiyle birlikte görünür. En şık sonuç budur.</li>
                        <li><strong>Normal fotoğraf (JPEG veya arka planlı PNG):</strong> bantla tutturulmuş bir fotoğraf baskısı gibi gösterilir.</li>
                        <li>“Ana fotoğraf” ürün kartında ve ürün sayfasında ilk görünen fotoğraftır. Diğerleri ürün sayfasında küçük resim olarak listelenir.</li>
                        <li>Kaydırma hareketleri, askıdaki sallanma ve fareyle eğilme efekti fotoğraflarda da aynen çalışır.</li>
                    </ul>
                </details>
            </div>
        </x-admin.section>

        {{-- 2. Basics --}}
        <x-admin.section number="2" title="Ürün bilgileri">
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="urun-adi" class="text-sm font-semibold">Ürün adı</label>
                    <input id="urun-adi" type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="80" placeholder="Örn. Toros Saha Ceketi" data-preview-source="name" @error('name') aria-invalid="true" @enderror class="field mt-1.5">
                    <x-admin.error for="name" />
                </div>

                <div>
                    <label for="urun-kodu" class="text-sm font-semibold">Ürün kodu</label>
                    <input id="urun-kodu" type="text" name="code" value="{{ old('code', $product->code) }}" required maxlength="20" data-preview-source="code" @error('code') aria-invalid="true" @enderror class="field mt-1.5 font-display text-lg font-bold tracking-wide uppercase">
                    <p class="mt-1.5 text-xs text-ink/60">Stok kodu. Boştaki ilk kod önerildi, istersen değiştir.</p>
                    <x-admin.error for="code" />
                </div>

                <div>
                    <label for="urun-kategori" class="text-sm font-semibold">Kategori</label>
                    <input id="urun-kategori" type="text" name="category" value="{{ old('category', $product->category) }}" required maxlength="40" list="kategoriler" placeholder="Örn. Dış giyim" data-preview-source="category" @error('category') aria-invalid="true" @enderror class="field mt-1.5">
                    <datalist id="kategoriler">
                        @foreach (array_unique([...$categories, 'Dış giyim', 'Alt giyim', 'Üst giyim', 'Ekipman', 'Aksesuar', 'Ayakkabı']) as $category)
                            <option value="{{ $category }}"></option>
                        @endforeach
                    </datalist>
                    <p class="mt-1.5 text-xs text-ink/60">Listeden seç ya da yeni bir kategori yaz.</p>
                    <x-admin.error for="category" />
                </div>

                <div class="sm:col-span-2">
                    <div class="flex items-baseline justify-between gap-4">
                        <label for="urun-kisa" class="text-sm font-semibold">Kısa açıklama</label>
                        <span data-counter-for="urun-kisa" class="text-xs text-ink/60 tabular-nums"></span>
                    </div>
                    <textarea id="urun-kisa" name="tagline" rows="2" required maxlength="160" placeholder="Ürünü tek cümleyle anlat. Örn. Rüzgârı kesen, suyu iten dört mevsim saha ceketi." data-preview-source="tagline" @error('tagline') aria-invalid="true" @enderror class="field mt-1.5 resize-y">{{ old('tagline', $product->tagline) }}</textarea>
                    <p class="mt-1.5 text-xs text-ink/60">Ürün kartında, ana sayfada ve paylaşım önizlemelerinde görünür.</p>
                    <x-admin.error for="tagline" />
                </div>

                <div class="sm:col-span-2">
                    <label for="urun-aciklama" class="text-sm font-semibold">Detaylı açıklama</label>
                    <textarea id="urun-aciklama" name="description" rows="6" required maxlength="3000" placeholder="Ürünü kim, nerede, nasıl kullanır? Kumaşı ve kesimi neden böyle?" @error('description') aria-invalid="true" @enderror class="field mt-1.5 resize-y">{{ old('description', $product->description) }}</textarea>
                    <x-admin.error for="description" />
                </div>
            </div>
        </x-admin.section>

        {{-- 3. Colours --}}
        <x-admin.section number="3" title="Renkler" hint="Ürünün satıldığı renkleri ekle. Bir renge fotoğraf eklersen, ziyaretçi o rengi seçtiğinde ürün fotoğrafı da o renge geçer. Tek renkli ürünlerde bu bölümü boş bırakabilirsin.">
            <div data-colors data-next-index="{{ count($colorRows) ? max(array_map('intval', array_keys($colorRows))) + 1 : 0 }}">
                <p class="text-sm font-semibold">Hızlı ekle</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ($colorPresets as $presetName => $presetHex)
                        <button type="button" data-color-preset data-name="{{ $presetName }}" data-hex="{{ $presetHex }}" class="inline-flex min-h-10 items-center gap-2 rounded-full border border-ink/20 bg-sand-100 py-1 pr-3.5 pl-1.5 text-sm font-medium hover:border-ink/50">
                            <span class="size-7 rounded-full border border-ink/20" style="background: {{ $presetHex }}"></span>
                            {{ $presetName }}
                        </button>
                    @endforeach
                </div>

                <ul data-color-list class="mt-5 grid gap-3 empty:hidden">
                    @foreach ($colorRows as $index => $color)
                        @include('admin.products.partials.color-row', ['index' => $index, 'color' => $color])
                    @endforeach
                </ul>

                <template data-color-template>
                    @include('admin.products.partials.color-row', ['index' => '__INDEX__', 'color' => []])
                </template>

                <button type="button" data-add-color class="btn btn-line-dark mt-4 min-h-11 text-sm">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
                    Başka bir renk ekle
                </button>
            </div>
        </x-admin.section>

        {{-- 4. Sizes --}}
        <x-admin.section number="4" title="Bedenler" hint="Satılan bedenlere dokunarak seç. Sitede seçtiğin sırayla görünür. Bedeni olmayan ürünlerde hiçbirini seçme.">
            <div data-sizes class="grid gap-5">
                @foreach ($sizeGroups as $groupLabel => $options)
                    <fieldset>
                        <legend class="text-sm font-semibold">{{ $groupLabel }}</legend>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($options as $option)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="sizes[]" value="{{ $option }}" class="sr-only" @checked(in_array($option, $selectedSizes, true))>
                                    <span class="size-chip">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach

                <fieldset>
                    <legend class="text-sm font-semibold">Listede olmayan bir beden</legend>
                    <div data-custom-sizes class="mt-2 flex flex-wrap gap-2 empty:hidden">
                        @foreach ($customSizes as $customSize)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="sizes[]" value="{{ $customSize }}" class="sr-only" checked>
                                <span class="size-chip">{{ $customSize }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div class="mt-2 flex max-w-sm gap-2">
                        <input type="text" data-size-input maxlength="20" placeholder="Örn. 4XL veya 58 cm" aria-label="Yeni beden" class="field">
                        <button type="button" data-add-size class="btn btn-line-dark shrink-0">Ekle</button>
                    </div>
                </fieldset>
                <x-admin.error for="sizes.*" />
            </div>
        </x-admin.section>

        {{-- 5. Highlights --}}
        <x-admin.section number="5" title="Öne çıkanlar" hint="Her satıra bir özellik yaz. Ürün sayfasında madde madde gösterilir.">
            <label for="urun-ozellikler" class="sr-only">Öne çıkanlar</label>
            <textarea id="urun-ozellikler" name="features" rows="6" maxlength="3000" placeholder="Su itici kaplamalı ripstop kumaş&#10;Kollarda arma takmaya uygun cırt panel&#10;Dört ön cep" class="field resize-y">{{ old('features', implode("\n", $product->features)) }}</textarea>
            <x-admin.error for="features" />
        </x-admin.section>

        {{-- 6. Technical details --}}
        <x-admin.section number="6" title="Teknik bilgiler" hint="Kumaş, ağırlık, ölçü gibi bilgiler. Soldaki kutuya bilgi adını, sağdakine değerini yaz. Boş bıraktığın satırlar kaydedilmez.">
            <div data-specs data-next-index="{{ count($specRows) ? max(array_map('intval', array_keys($specRows))) + 1 : 0 }}">
                <datalist id="bilgi-adlari">
                    @foreach (['Kumaş', 'Astar', 'Ağırlık', 'Bakım', 'Ölçüler', 'Hacim', 'Kesim', 'Malzeme', 'Taban', 'Kapanış', 'Üretim yeri'] as $label)
                        <option value="{{ $label }}"></option>
                    @endforeach
                </datalist>

                <ul data-spec-list class="grid gap-3">
                    @foreach ($specRows as $index => $spec)
                        @include('admin.products.partials.spec-row', ['index' => $index, 'spec' => $spec])
                    @endforeach
                </ul>

                <template data-spec-template>
                    @include('admin.products.partials.spec-row', ['index' => '__INDEX__', 'spec' => []])
                </template>

                <button type="button" data-add-spec class="btn btn-line-dark mt-4 min-h-11 text-sm">
                    <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14" /></svg>
                    Satır ekle
                </button>
            </div>
        </x-admin.section>

        {{-- 7. Publishing --}}
        <x-admin.section number="7" title="Yayın">
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <input type="hidden" name="is_published" value="0">
                    <label class="flex cursor-pointer items-start gap-4">
                        <input type="checkbox" name="is_published" value="1" class="peer sr-only" @checked(old('is_published', $product->is_published))>
                        <span class="relative mt-0.5 h-8 w-14 shrink-0 rounded-full bg-ink/25 transition-colors peer-checked:bg-olive-600 peer-focus-visible:outline-3 peer-focus-visible:outline-offset-3 peer-focus-visible:outline-coyote-400 after:absolute after:top-1 after:left-1 after:size-6 after:rounded-full after:bg-sand-50 after:shadow after:transition-transform peer-checked:after:translate-x-6" aria-hidden="true"></span>
                        <span>
                            <span class="block font-semibold">Sitede yayınla</span>
                            <span class="block text-sm text-ink/70">Kapalıysa ürün kaydedilir ama sitede görünmez. Hazırlık aşamasındaki ürünler için kullan.</span>
                        </span>
                    </label>
                </div>

                <div>
                    <label for="urun-sira" class="text-sm font-semibold">Sıra</label>
                    <input id="urun-sira" type="number" name="sort_order" value="{{ old('sort_order', $product->sort_order) }}" min="0" max="9999" inputmode="numeric" class="field mt-1.5 max-w-36">
                    <p class="mt-1.5 text-xs text-ink/60">Küçük sayı önce görünür. Örn. 10, 20, 30 diye ilerlersen araya ürün eklemek kolay olur.</p>
                    <x-admin.error for="sort_order" />
                </div>
            </div>
        </x-admin.section>
    </div>

    {{-- Live preview of the product card --}}
    <aside class="lg:sticky lg:top-24 lg:self-start" aria-label="Önizleme">
        <p class="text-sm font-semibold">Önizleme</p>
        <p class="text-xs text-ink/60">Ana sayfadaki ürün kartı böyle görünecek.</p>
        <div class="mt-3 overflow-hidden rounded-lg bg-olive-900 pb-6">
            <div class="rod"></div>
            <div class="hanger mx-auto w-fit [--string:2.5rem]">
                <article class="tag grain flex w-[min(78vw,19rem)] flex-col gap-3 px-5 pt-14 pb-5">
                    <div class="flex items-baseline justify-between gap-4">
                        <span data-preview="code" class="font-display text-xl font-bold tracking-wide">{{ $product->code }}</span>
                        <span data-preview="category" class="text-sm text-ink/70">{{ $product->category }}</span>
                    </div>
                    <div data-preview-visual class="grid h-44 place-items-center">
                        @if ($product->images || $product->hasIllustration())
                            <x-product-visual :product="$product" class="h-44" />
                        @else
                            <p class="text-center text-sm text-ink/50">Fotoğraf seçince burada görünür.</p>
                        @endif
                    </div>
                    <p data-preview="name" data-placeholder="Ürün adı" class="display text-[2.2rem]">{{ $product->name ?: 'Ürün adı' }}</p>
                    <p data-preview="tagline" data-placeholder="Kısa açıklama burada görünür." class="line-clamp-3 text-[0.9rem] leading-snug text-ink/80">{{ $product->tagline ?: 'Kısa açıklama burada görünür.' }}</p>
                    <span class="btn btn-olive pointer-events-none mt-1 min-h-11 text-sm" aria-hidden="true">Ürünü incele</span>
                </article>
            </div>
        </div>
    </aside>

    {{-- Save bar --}}
    <div class="sticky bottom-0 z-20 -mx-4 border-t border-ink/10 bg-sand-100/95 px-4 pt-3 pb-[calc(0.75rem+env(safe-area-inset-bottom))] backdrop-blur sm:mx-0 sm:rounded-t-lg sm:px-5 lg:col-span-2">
        <div class="flex items-center justify-end gap-3">
            <p data-dirty-hint hidden class="mr-auto hidden text-sm text-ink/70 sm:block">Kaydedilmemiş değişiklikler var.</p>
            <a href="{{ route('admin.products.index') }}" class="btn btn-line-dark min-h-12">Vazgeç</a>
            <button type="submit" data-save class="btn btn-olive min-h-12 min-w-36 disabled:cursor-wait disabled:opacity-70">{{ $submitLabel }}</button>
        </div>
    </div>
</form>
