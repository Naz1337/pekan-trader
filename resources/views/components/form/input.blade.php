@props([
    'id',
    'name' => null,
    'type' => 'text',
    'value' => null,
    'label',
    'required' => true,
    'basis' => '200',
    'textarea' => false,
    'accept' => null,
    'min' => null,
    'max' => null
])

<div {{ $attributes->merge(['class' => 'flex'])->except('x-model') }}>
    <label for="{{ $id }}" class="basis-[{{ $basis }}px] text-base-content/60 mt-2">{{ $label }}</label>
    @if ($type === 'slot')
        <div class="grow flex flex-col justify-center">
            {{ $slot }}
        </div>
    @else
        @if(!$textarea)
            @if ($type === 'file')
            <x-form.saner-upload-input :id="$id" :accept="$accept ?? '*'" :required="$required"/>
            @else
            <input type="{{ $type }}" id="{{ $id }}" name="{{ $name ?? $id  }}"  value="{{ $value }}"
                   class="input grow" {{ $required ? 'required' : '' }} {{ $max ? 'max='. $max : '' }}
                   {{ $min ? 'min='. $min : '' }}  {{ $attributes->only('x-model') }}
                   {{ $attributes->only('x-bind:disabled') }} >
            @endif
        @else
        <textarea id="{{ $id }}" name="{{ $name ?? $id  }}" class="textarea grow" {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
        @endif
    @endif
</div>
