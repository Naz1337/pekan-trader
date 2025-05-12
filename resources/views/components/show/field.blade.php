@props([
    'label',
    'value' => null,
    'isBold' => false
])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    <div class="text-sm text-base-content/60 mb-1">{{ $label }}</div>
    @if (is_null($value))
        <div>
            {{ $slot }}
        </div>
    @else
        <div @class(['font-semibold' => $isBold])>
            {{ $value }}
        </div>
    @endif

</div>
