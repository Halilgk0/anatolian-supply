@props(['x' => 0, 'y' => 0, 'width' => 30, 'rotate' => 0])

@php
    $height = $width / 1.5;
@endphp

{{-- Turkish flag patch; geometry follows the official flag ratios (G = patch height) --}}
<g transform="translate({{ $x }} {{ $y }}) rotate({{ $rotate }} {{ $width / 2 }} {{ $height / 2 }})">
    <rect width="{{ $width }}" height="{{ $height }}" rx="{{ $height * 0.08 }}" fill="#c8102e" />
    <g transform="translate({{ $height / 2 }} {{ $height / 2 }}) scale({{ $height / 100 }})" fill="#f6f1e6">
        <path d="M21.125,-13.369 A25,25 0 1 0 21.125,13.369 A20,20 0 1 1 21.125,-13.369 Z" />
        <polygon transform="translate(45.8 0) scale(12.5)" points="-1,0 -0.309,-0.2245 -0.309,-0.951 0.118,-0.363 0.809,-0.588 0.382,0 0.809,0.588 0.118,0.363 -0.309,0.951 -0.309,0.2245" />
    </g>
</g>
