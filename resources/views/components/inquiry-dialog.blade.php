@props(['product', 'sizeRequired' => false])

@php
    $fields = [
        ['name' => 'name', 'label' => 'Ad soyad', 'type' => 'text', 'autocomplete' => 'name', 'required' => true],
        ['name' => 'email', 'label' => 'E-posta', 'type' => 'email', 'autocomplete' => 'email', 'required' => true],
        ['name' => 'phone', 'label' => 'Telefon (isteğe bağlı)', 'type' => 'tel', 'autocomplete' => 'tel', 'required' => false],
    ];
    $defaultSize = $sizeRequired ? '' : ($product->sizes[0] ?? '');
    $defaultColor = $product->colors[0]['name'] ?? '';
    $canSendMail = \App\Mail\ProductInquiryMail::canBeDelivered();
@endphp

<dialog
    data-inquiry-dialog
    aria-labelledby="bilgi-al-baslik"
    class="sheet"
    @if ($errors->any()) open data-open-on-load @endif
>
    <div class="grain px-5 pt-5 pb-[calc(1.5rem+env(safe-area-inset-bottom))] sm:px-8 sm:pt-7 sm:pb-8">
        <div class="mx-auto mb-3 h-1 w-10 rounded-full bg-ink/20 sm:hidden" aria-hidden="true"></div>

        <div class="flex items-start justify-between gap-4">
            <h2 id="bilgi-al-baslik" class="display text-[2.4rem] sm:text-5xl">{{ $product->name }} için bilgi al</h2>
            <button type="button" data-close-inquiry class="-mt-1 -mr-2 grid size-11 shrink-0 place-items-center rounded-full hover:bg-ink/10">
                <span class="sr-only">Kapat</span>
                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" /></svg>
            </button>
        </div>

        <div data-inquiry-body>
            @if ($canSendMail)
                <p class="mt-2 text-[0.95rem] leading-relaxed text-ink/75">Mesajın ürün bilgileriyle birlikte {{ config('store.inquiry_email') }} adresine gider. Cevabımızı yazdığın e-posta adresine alırsın.</p>
            @else
                {{-- Mail is not configured on this server yet: offer the visitor's own mail app instead of a form that cannot deliver. --}}
                <p class="mt-2 text-[0.95rem] leading-relaxed text-ink/75">Ürün kodu, renk ve beden bilgisiyle hazırlanmış mesajı e-posta uygulamanda açıp {{ config('store.inquiry_email') }} adresine gönderebilirsin.</p>
                <a data-mailto href="{{ $product->mailtoLink(config('store.inquiry_email')) }}" class="btn btn-olive mt-5 min-h-14 w-full text-base">
                    <x-icons.mail class="size-5" />
                    E-posta uygulamasında aç
                </a>
            @endif

            <form data-inquiry-form method="POST" action="{{ route('products.inquiries.store', $product->slug) }}" class="mt-5 grid gap-4" novalidate @unless ($canSendMail) hidden @endunless>
                @csrf
                <input type="hidden" name="color" value="{{ old('color', $defaultColor) }}" data-inquiry-color>
                <input type="hidden" name="size" value="{{ old('size', $defaultSize) }}" data-inquiry-size>
                <div class="hidden" aria-hidden="true">
                    <label>Web sitesi <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <p class="rounded-sm bg-sand-200 px-3 py-2.5 text-sm leading-snug">
                    {{ $product->code }}@if ($product->colors), renk: <strong data-summary-color>{{ old('color', $defaultColor) }}</strong>@endif @if ($product->sizes), beden: <strong data-summary-size>{{ old('size', $defaultSize) ?: 'seçilmedi' }}</strong>@endif
                </p>

                @foreach ($fields as $field)
                    <div class="grid gap-1.5">
                        <label for="talep-{{ $field['name'] }}" class="text-sm font-semibold">{{ $field['label'] }}</label>
                        <input
                            id="talep-{{ $field['name'] }}"
                            type="{{ $field['type'] }}"
                            name="{{ $field['name'] }}"
                            value="{{ old($field['name']) }}"
                            autocomplete="{{ $field['autocomplete'] }}"
                            aria-describedby="talep-{{ $field['name'] }}-hata"
                            @if ($field['required']) required @endif
                            @error($field['name']) aria-invalid="true" @enderror
                            class="field"
                        >
                        <p id="talep-{{ $field['name'] }}-hata" data-error-for="{{ $field['name'] }}" class="text-sm text-signal">@error($field['name']){{ $message }}@enderror</p>
                    </div>
                @endforeach

                <div class="grid gap-1.5">
                    <label for="talep-message" class="text-sm font-semibold">Mesaj</label>
                    <textarea
                        id="talep-message"
                        name="message"
                        rows="4"
                        required
                        aria-describedby="talep-message-hata"
                        @error('message') aria-invalid="true" @enderror
                        class="field resize-y"
                    >{{ old('message', $product->inquiryMessage()) }}</textarea>
                    <p id="talep-message-hata" data-error-for="message" class="text-sm text-signal">@error('message'){{ $message }}@enderror</p>
                </div>

                <p data-inquiry-status role="alert" class="text-[0.95rem] font-medium text-signal empty:hidden">@error('inquiry'){{ $message }}@enderror</p>

                <button type="submit" data-inquiry-submit class="btn btn-olive min-h-14 text-base disabled:cursor-wait disabled:opacity-70">
                    <x-icons.mail class="size-5" />
                    <span data-submit-label>Talebi gönder</span>
                </button>
            </form>
        </div>

        <div data-inquiry-success hidden class="mt-5 grid gap-4" role="status">
            <p class="rounded-sm border-l-4 border-olive-600 bg-sand-200 px-4 py-3 leading-relaxed" data-success-message></p>
            <button type="button" data-close-inquiry class="btn btn-olive min-h-12">Tamam</button>
        </div>

        @if ($canSendMail)
            <p class="mt-5 text-sm leading-relaxed text-ink/75">
                Formu doldurmak yerine kendi e-posta uygulamanı kullanabilirsin:
                <a data-mailto href="{{ $product->mailtoLink(config('store.inquiry_email')) }}" class="font-semibold text-ink underline underline-offset-4">hazır mesajı e-posta uygulamanda aç</a>.
            </p>
        @endif
    </div>
</dialog>
