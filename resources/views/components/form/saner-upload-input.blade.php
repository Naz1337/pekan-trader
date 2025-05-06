@props([
    'id',
    'name' => null,
    'accept' => 'image/*',
    'multiple' => false,
    'required' => true,
])
<div class="flex" x-data="{
    truncateString (str) {
        if (str.length > 30) {
            return str.substring(0, 27) + '...';
        } else {
            return str;
        }
    }
}" {{ $attributes }}>
    <input
        type="file"
        name="{{ $name ?? $id }}" id="{{ $id }}"
        accept="{{ $accept }}"
        {{ $required ? 'required' : '' }}
        class="hidden"
        x-ref="fileInput"
        {{ $multiple ? 'multiple' : '' }}
        @@change="
            const file = $event.target.files[0];

            $refs.fileButton.innerText = truncateString(`${file.name}`);
            $refs.fileButton.title = file.name;
        "
    >
    <button
        class="btn btn-secondary btn-soft truncate text-ellipsis
            max-w-[100%]"
        @@click.prevent="$refs.fileInput.click()"
        x-ref="fileButton"
        @@reset="
            $refs.fileButton.innerText = 'Select a File';
        "
    >
        Select a File
    </button>
</div>
