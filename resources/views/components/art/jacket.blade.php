<svg {{ $attributes->merge(['viewBox' => '0 0 400 440', 'aria-hidden' => 'true']) }}>
    <ellipse cx="200" cy="421" rx="160" ry="9" fill="#000" opacity=".14" />

    {{-- Rolled hood behind the collar --}}
    <path fill="var(--art-shade)" d="M150,46 Q200,26 250,46 L246,70 Q200,56 154,70 Z" />

    {{-- Sleeves --}}
    <path fill="var(--art-main)" d="M108,78 C82,84 66,98 60,118 L36,350 L84,360 L114,168 Z" />
    <path fill="var(--art-main)" d="M292,78 C318,84 334,98 340,118 L364,350 L316,360 L286,168 Z" />
    <path fill="var(--art-shade)" opacity=".7" d="M114,168 L84,360 L71,358 L103,160 Z" />
    <path fill="var(--art-shade)" opacity=".7" d="M286,168 L316,360 L329,358 L297,160 Z" />
    <path fill="var(--art-detail)" d="M38,331 L86,340 L84,360 L36,350 Z" />
    <path fill="var(--art-detail)" d="M362,331 L314,340 L316,360 L364,350 Z" />

    {{-- Torso --}}
    <path fill="var(--art-main)" d="M108,78 L158,56 Q200,74 242,56 L292,78 Q296,120 290,160 L288,398 L112,398 L110,160 Q104,120 108,78 Z" />
    <path fill="var(--art-shade)" opacity=".55" d="M110,160 L112,398 L128,398 L126,170 Z" />
    <path fill="var(--art-shade)" opacity=".55" d="M290,160 L288,398 L272,398 L274,170 Z" />
    <path fill="none" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" d="M108,80 Q112,122 110,160 M292,80 Q288,122 290,160" />

    {{-- Stand collar and zip placket --}}
    <path fill="var(--art-shade)" d="M156,52 Q200,72 244,52 L248,74 Q200,96 152,74 Z" />
    <rect fill="var(--art-shade)" x="193" y="84" width="14" height="314" />
    <line x1="200" y1="88" x2="200" y2="396" stroke="var(--art-detail)" stroke-width="2" stroke-dasharray="3 3" />
    @foreach ([122, 182, 242, 302] as $snapY)
        <circle cx="200" cy="{{ $snapY }}" r="3.5" fill="var(--art-detail)" />
    @endforeach

    {{-- Epaulets --}}
    <path fill="var(--art-shade)" d="M114,84 L152,66 L156,76 L118,94 Z" />
    <path fill="var(--art-shade)" d="M286,84 L248,66 L244,76 L282,94 Z" />
    <circle cx="148" cy="73" r="2.6" fill="var(--art-detail)" />
    <circle cx="252" cy="73" r="2.6" fill="var(--art-detail)" />

    {{-- Chest pockets --}}
    @foreach ([128, 218] as $pocketX)
        <rect x="{{ $pocketX }}" y="138" width="54" height="62" rx="3" fill="var(--art-main)" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" />
        <path fill="var(--art-shade)" d="M{{ $pocketX - 2 }},134 L{{ $pocketX + 56 }},134 L{{ $pocketX + 56 }},154 L{{ $pocketX + 27 }},161 L{{ $pocketX - 2 }},154 Z" />
        <circle cx="{{ $pocketX + 27 }}" cy="153" r="3" fill="var(--art-detail)" />
    @endforeach

    {{-- Lower pockets --}}
    @foreach ([124, 214] as $pocketX)
        <rect x="{{ $pocketX }}" y="262" width="62" height="76" rx="3" fill="var(--art-main)" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" />
        <rect x="{{ $pocketX - 2 }}" y="256" width="66" height="24" rx="2" fill="var(--art-shade)" />
        <circle cx="{{ $pocketX + 31 }}" cy="271" r="3" fill="var(--art-detail)" />
    @endforeach

    {{-- Drawcord channel and hem --}}
    <line x1="112" y1="372" x2="288" y2="372" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" />
    <rect x="112" y="380" width="176" height="18" fill="var(--art-shade)" />

    {{-- Hook-and-loop sleeve panels with a flag patch --}}
    <rect x="62" y="140" width="36" height="44" rx="3" fill="var(--art-shade)" transform="rotate(6 80 162)" />
    <rect x="302" y="140" width="36" height="44" rx="3" fill="var(--art-shade)" transform="rotate(-6 320 162)" />
    <x-art.flag-patch :x="305" :y="152" :width="30" :rotate="-6" />
</svg>
