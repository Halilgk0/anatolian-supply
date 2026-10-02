@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'mt-1.5 text-sm font-medium text-signal']) }}>{{ $message }}</p>
@enderror
