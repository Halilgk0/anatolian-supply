<svg {{ $attributes->merge(['viewBox' => '0 0 400 440', 'aria-hidden' => 'true']) }}>
    <ellipse cx="200" cy="423" rx="130" ry="9" fill="#000" opacity=".16" />

    {{-- Shoulder straps behind the bag --}}
    <path fill="var(--art-detail)" d="M104,96 Q94,190 104,300 L118,300 L118,96 Z" />
    <path fill="var(--art-detail)" d="M296,96 Q306,190 296,300 L282,300 L282,96 Z" />

    {{-- Grab handle --}}
    <path d="M172,52 Q200,16 228,52" fill="none" stroke="var(--art-detail)" stroke-width="10" stroke-linecap="round" />

    {{-- Body --}}
    <path fill="var(--art-main)" d="M114,98 Q114,46 164,46 L236,46 Q286,46 286,98 L292,390 Q292,412 270,412 L130,412 Q108,412 108,390 Z" />
    <path fill="var(--art-shade)" opacity=".6" d="M114,98 Q114,46 164,46 L172,46 Q130,52 129,104 L125,412 L130,412 Q108,412 108,390 Z" />
    <path fill="var(--art-shade)" opacity=".6" d="M286,98 Q286,46 236,46 L228,46 Q270,52 271,104 L275,412 L270,412 Q292,412 292,390 Z" />
    <path d="M126,104 Q126,62 166,60 L234,60 Q274,62 274,104" fill="none" stroke="var(--art-detail)" stroke-width="4" stroke-dasharray="2 2" />

    {{-- Admin pocket with a flag patch --}}
    <rect x="144" y="92" width="112" height="104" rx="12" fill="var(--art-shade)" />
    <line x1="156" y1="104" x2="244" y2="104" stroke="var(--art-detail)" stroke-width="3" stroke-dasharray="2 2" />
    <rect x="238" y="100" width="6" height="16" rx="2" fill="var(--art-detail)" />
    <rect x="168" y="122" width="64" height="56" rx="4" fill="var(--art-detail)" opacity=".35" />
    <x-art.flag-patch :x="180" :y="137" :width="40" />

    {{-- Compression straps --}}
    @foreach ([150, 300] as $strapY)
        <rect x="100" y="{{ $strapY }}" width="26" height="10" rx="2" fill="var(--art-detail)" />
        <rect x="274" y="{{ $strapY }}" width="26" height="10" rx="2" fill="var(--art-detail)" />
        <rect x="94" y="{{ $strapY - 3 }}" width="11" height="16" rx="2.5" fill="var(--art-detail)" />
        <rect x="295" y="{{ $strapY - 3 }}" width="11" height="16" rx="2.5" fill="var(--art-detail)" />
    @endforeach

    {{-- Front pocket with MOLLE webbing --}}
    <rect x="132" y="222" width="136" height="160" rx="16" fill="var(--art-shade)" />
    @foreach ([240, 266, 292, 318, 344] as $rowY)
        <rect x="132" y="{{ $rowY }}" width="136" height="12" fill="var(--art-detail)" />
        @foreach ([158, 184, 210, 236] as $tackX)
            <line x1="{{ $tackX }}" y1="{{ $rowY }}" x2="{{ $tackX }}" y2="{{ $rowY + 12 }}" stroke="var(--art-main)" stroke-width="2" />
        @endforeach
    @endforeach

    {{-- Base --}}
    <path fill="var(--art-detail)" opacity=".8" d="M108,386 L292,386 L292,390 Q292,412 270,412 L130,412 Q108,412 108,390 Z" />
</svg>
