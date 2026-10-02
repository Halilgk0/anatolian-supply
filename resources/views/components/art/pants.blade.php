<svg {{ $attributes->merge(['viewBox' => '0 0 400 440', 'aria-hidden' => 'true']) }}>
    <ellipse cx="200" cy="423" rx="140" ry="8" fill="#000" opacity=".14" />

    {{-- Legs --}}
    <path fill="var(--art-main)" d="M120,52 L280,52 C286,120 292,250 298,412 L214,412 L201,168 L199,168 L186,412 L102,412 C108,250 114,120 120,52 Z" />
    <path fill="var(--art-shade)" opacity=".6" d="M199,168 L186,412 L172,412 L190,180 Z" />
    <path fill="var(--art-shade)" opacity=".6" d="M201,168 L214,412 L228,412 L210,180 Z" />
    <path fill="var(--art-shade)" d="M188,148 L212,148 L201,176 L199,176 Z" />
    <path fill="none" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" d="M116,120 C112,220 108,320 106,396 M284,120 C288,220 292,320 294,396" />

    {{-- Waistband, belt loops and fly --}}
    <rect x="118" y="34" width="164" height="24" rx="2" fill="var(--art-shade)" />
    <path fill="none" stroke="var(--art-detail)" stroke-width="1.2" stroke-dasharray="3 3" d="M120,39 L280,39 M120,53 L280,53" />
    @foreach ([132, 170, 221, 259] as $loopX)
        <rect x="{{ $loopX }}" y="30" width="9" height="34" rx="1.5" fill="var(--art-detail)" />
    @endforeach
    <circle cx="200" cy="46" r="5" fill="var(--art-detail)" />
    <path fill="none" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" d="M207,60 L207,128 Q207,142 196,146" />

    {{-- Slant pockets --}}
    <path fill="none" stroke="var(--art-detail)" stroke-width="2" d="M146,58 Q150,100 122,112 M254,58 Q250,100 278,112" />

    {{-- Cargo pockets --}}
    @foreach ([116, 234] as $pocketX)
        <rect x="{{ $pocketX }}" y="214" width="50" height="78" rx="3" fill="var(--art-main)" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" />
        <rect x="{{ $pocketX - 2 }}" y="206" width="54" height="26" rx="2" fill="var(--art-shade)" />
        <circle cx="{{ $pocketX + 25 }}" cy="222" r="3" fill="var(--art-detail)" />
        <line x1="{{ $pocketX + 25 }}" y1="240" x2="{{ $pocketX + 25 }}" y2="288" stroke="var(--art-shade)" stroke-width="2" />
    @endforeach

    {{-- Reinforced knees --}}
    <path fill="var(--art-shade)" opacity=".75" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" d="M116,302 L188,302 L186,352 L114,352 Z" />
    <path fill="var(--art-shade)" opacity=".75" stroke="var(--art-detail)" stroke-width="1.5" stroke-dasharray="4 3" d="M284,302 L212,302 L214,352 L286,352 Z" />

    {{-- Drawcord hems --}}
    <path fill="var(--art-shade)" d="M103,396 L187,396 L186,412 L102,412 Z" />
    <path fill="var(--art-shade)" d="M213,396 L297,396 L298,412 L214,412 Z" />
    <rect x="96" y="400" width="7" height="12" rx="2" fill="var(--art-detail)" />
    <rect x="297" y="400" width="7" height="12" rx="2" fill="var(--art-detail)" />
</svg>
