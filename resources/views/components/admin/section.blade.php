@props(['number', 'title', 'hint' => null])

<section {{ $attributes->merge(['class' => 'rounded-lg border border-ink/10 bg-sand-50/70 p-5 sm:p-7']) }}>
    <div class="flex items-start gap-4">
        <span class="display grid size-10 shrink-0 place-items-center rounded-full bg-olive-900 text-xl text-sand-100" aria-hidden="true">{{ $number }}</span>
        <div>
            <h2 class="display text-[2rem] sm:text-4xl">{{ $title }}</h2>
            @if ($hint)
                <p class="mt-2 max-w-prose text-[0.95rem] leading-relaxed text-ink/70">{{ $hint }}</p>
            @endif
        </div>
    </div>

    <div class="mt-6">
        {{ $slot }}
    </div>
</section>
