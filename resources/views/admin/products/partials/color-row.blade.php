{{-- One colour option. Rendered with a real index, or with __INDEX__ inside the <template> used to add rows. --}}
@php
    $existingImage = $color['existing_image'] ?? null;
@endphp

<li data-color-row class="rounded-md border border-ink/10 bg-sand-100 p-3">
    <div class="grid grid-cols-[auto_1fr_auto] items-center gap-3">
        <input
            type="color"
            name="colors[{{ $index }}][hex]"
            value="{{ $color['hex'] ?? '#5f6744' }}"
            aria-label="Renk tonu"
            class="size-12 cursor-pointer rounded border border-ink/20 bg-sand-50 p-1"
        >
        <input
            type="text"
            name="colors[{{ $index }}][name]"
            value="{{ $color['name'] ?? '' }}"
            required
            maxlength="30"
            placeholder="Renk adı, örn. Zeytin"
            aria-label="Renk adı"
            class="field"
        >
        <button type="button" data-remove-row class="grid size-11 place-items-center rounded-full text-ink/70 hover:bg-ink/10 hover:text-ink">
            <span class="sr-only">Bu rengi kaldır</span>
            <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
        </button>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-3 pl-[3.75rem]">
        <img data-color-preview src="{{ $existingImage ? \App\Models\Product::mediaUrl($existingImage) : '' }}" alt="" @unless ($existingImage) hidden @endunless class="size-12 rounded bg-sand-200 object-cover">
        <label class="btn btn-line-dark min-h-10 cursor-pointer px-3 text-sm">
            <svg viewBox="0 0 24 24" class="size-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" /><circle cx="9" cy="10" r="1.6" /><path d="m21 16-5-5-8 8" /></svg>
            <span data-color-photo-label>{{ $existingImage ? 'Fotoğrafı değiştir' : 'Bu renge fotoğraf ekle' }}</span>
            <input type="file" name="colors[{{ $index }}][image]" accept="image/jpeg,image/png,image/webp" data-color-image class="sr-only">
        </label>
        <input type="hidden" name="colors[{{ $index }}][existing_image]" value="{{ $existingImage }}">
        @if ($existingImage)
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="colors[{{ $index }}][remove_image]" value="1" data-color-remove-image class="size-4 accent-signal">
                Fotoğrafı kaldır
            </label>
        @else
            <span class="text-xs text-ink/60">İsteğe bağlı</span>
        @endif
    </div>

    @if (is_int($index) || ctype_digit((string) $index))
        <x-admin.error for="colors.{{ $index }}.name" class="pl-[3.75rem]" />
        <x-admin.error for="colors.{{ $index }}.hex" class="pl-[3.75rem]" />
        <x-admin.error for="colors.{{ $index }}.image" class="pl-[3.75rem]" />
    @endif
</li>
