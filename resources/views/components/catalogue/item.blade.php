@use('Illuminate\Support\Str')
@use('Illuminate\Support\Facades\Storage')
@props([
    'product'
])

<a class="card bg-base-200 w-72 hover:shadow-xl active:shadow-md transition-all duration-300 group"
   href="{{ route('catalogue.show', compact('product')) }}">
    <figure class="relative h-48 bg-base-300">
        {{-- "New" Badge --}}
        @if($product->created_at->gt(now()->subDays(7)))
            <div class="badge badge-accent badge-sm absolute top-2 right-2 z-10 font-semibold">NEW</div>
        @endif

        <img src="{{ $product->main_image_url }}" alt="{{ $product->name }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-in-out">

        {{-- Stock Indicator (Overlay on image or below) --}}
        @if($product->stock_quantity <= 0)
            <div class="absolute bottom-2 left-1/2 -translate-x-1/2 z-10">
                <span class="badge badge-error badge-outline font-medium">Out of Stock</span>
            </div>
        @elseif($product->stock_quantity > 0 && $product->stock_quantity <= 10)
            <div class="absolute bottom-2 right-2 z-10">
                <span class="badge badge-warning badge-outline badge-sm font-medium">Low Stock</span>
            </div>
        @endif
    </figure>

    <div class="card-body p-4 flex flex-col justify-between">
        <div>
            {{-- Category --}}
            @if($product->category)
                <div class="text-xs text-base-content/70 mb-1.5 flex items-center">
                    <x-icon.tag class="w-3 h-3 mr-1.5 text-primary"/>
                    <span>{{ $product->category->name }}</span>
                </div>
            @endif

            <h2 class="card-title text-md font-semibold mb-1 leading-snug hover:text-primary transition-colors">
                {{ $product->name }}
            </h2>

            {{-- Short Description --}}
            <div class="text-primary font-bold text-lg mb-2">
                RM {{ number_format($product->price, 2) }}
            </div>

            {{-- Fulfilled Orders Count - NEW SECTION --}}
            @php
                $fulfilledQuantity = 0;
                // Ensure the product model has the orderItems relationship defined
                // and that orderItems has an 'order' relationship.
                if (method_exists($product, 'orderItems')) {
                    $fulfilledQuantity = $product->orderItems()
                        ->whereHas('order', function ($query) {
                            $query->where('status', 'completed'); // Assuming 'completed' is the status for fulfilled orders
                        })
                        ->sum('quantity');
                }
            @endphp

            @if($fulfilledQuantity > 0)
            <div class="mb-3 flex items-center gap-1.5">
                <x-icon.star class="w-4 h-4 text-yellow-500"/>
                <span class="font-semibold text-sm text-base-content/90">{{ $fulfilledQuantity }}</span>
                <span class="text-xs text-base-content/70">sold</span>
            </div>
            @endif
            {{-- END NEW SECTION --}}

            {{-- Short Description --}}
            <p class="text-sm text-base-content/80 mb-3 h-10 overflow-hidden">
                {{ Str::limit($product->description, 70) }}
            </p>
        </div>

        <div class="mt-auto pt-2 border-t border-base-300">
            <div class="flex gap-2 text-xs items-center text-base-content/70">
                <x-icon.store class="w-3.5 h-3.5 shrink-0 text-base-content/80"/>
                <span class="truncate" title="{{ $product->seller->business_name }}">
                    {{ $product->seller->business_name }}
                </span>
            </div>
        </div>
    </div>
</a>
