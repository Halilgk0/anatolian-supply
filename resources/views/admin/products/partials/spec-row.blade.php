{{-- One "technical details" line. Rendered with a real index, or with __INDEX__ inside the <template> used to add rows. --}}
<li data-spec-row class="grid grid-cols-[1fr_auto] gap-2 sm:grid-cols-[minmax(0,14rem)_1fr_auto]">
    <input
        type="text"
        name="specs[{{ $index }}][label]"
        value="{{ $spec['label'] ?? '' }}"
        list="bilgi-adlari"
        maxlength="40"
        placeholder="Bilgi adı, örn. Kumaş"
        aria-label="Bilgi adı"
        class="field"
    >
    <input
        type="text"
        name="specs[{{ $index }}][value]"
        value="{{ $spec['value'] ?? '' }}"
        maxlength="160"
        placeholder="Bilgi, örn. %100 pamuk ripstop"
        aria-label="Bilgi"
        class="field col-span-2 row-start-2 sm:col-span-1 sm:row-start-auto"
    >
    <button type="button" data-remove-row class="col-start-2 row-start-1 grid size-12 place-items-center rounded-full text-ink/70 hover:bg-ink/10 hover:text-ink sm:col-start-auto">
        <span class="sr-only">Bu satırı kaldır</span>
        <svg viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
    </button>

    @if (is_int($index) || ctype_digit((string) $index))
        <x-admin.error for="specs.{{ $index }}.label" class="col-span-full" />
        <x-admin.error for="specs.{{ $index }}.value" class="col-span-full" />
    @endif
</li>
