@use('Illuminate\Support\Facades\Storage')
@props([
    'product'
])

<a class="card bg-base-200 w-46 hover:bg-base-300/70 hover:shadow-sm
          active:bg-base-300 active:shadow-lg transition" href="{{ route('catalogue.show', compact('product')) }}">
    <figure>
        <img src="{{ Storage::url($product->image_path) }}" alt="" class="w-46 h-46 object-contain">
    </figure>
    <div class="card-body justify-between">
        <div>
            <h2 class="card-title">{{ $product->name }}</h2>
            <div class="text-primary">RM {{ number_format($product->price, 2) }}</div>
        </div>

        <div class="">
            <div class="divider m-0"></div>

            <div class="flex gap-2 text-sm items-baseline text-base-content/60">
                <x-icon.user-circle class="max-h-[1em] shrink-0 relative top-[0.125em] fill-base-content/60"/>
                <div>{{ $product->seller->business_name }}</div>
            </div>
        </div>

    </div>
</a>
