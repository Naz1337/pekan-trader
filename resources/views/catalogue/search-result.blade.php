<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        {{-- Search Bar (similar to welcome.blade.php) --}}
        <div class="flex gap-4 justify-center mb-10 w-full max-w-lg">
            {{-- Assuming the search is handled by GET request or JavaScript --}}
            <form action="" method="GET" class="w-full"> {{-- Example route --}}
                <label for="search" class="input input-bordered input-lg flex items-center gap-2 w-full">
                    <input type="text" name="query" id="search" class="grow" placeholder="Search products..." value="{{ request('query') }}">
                    <button type="submit" class="btn btn-primary btn-square">
                        <x-icon.search class="h-6 w-6" />
                    </button>
                </label>
            </form>
        </div>

        {{-- Search Results List --}}
        <div class="w-full max-w-3xl">
            @if(isset($products) && $products->count() > 0)
                <ul class="space-y-4">
                    @foreach($products as $product)
                        @php
                            // Calculate total quantity of this product sold in completed orders.
                            // This relies on the 'orderItems' relationship on the Product model
                            // and the 'order' relationship on the OrderItem model.
                            $fulfilledQuantity = 0;
                            if (method_exists($product, 'orderItems')) {
                                $fulfilledQuantity = $product->orderItems()
                                    ->whereHas('order', function ($query) {
                                        $query->where('status', 'completed');
                                    })
                                    ->sum('quantity');
                            }
                        @endphp
                        <li class="p-0 bg-transparent shadow-none hover:bg-transparent">
                            <a href="{{ route('catalogue.show', $product->id) }}" class="flex items-center bg-base-200 p-4 rounded-xl shadow-md hover:bg-base-300 transition-colors duration-150 ease-in-out no-underline text-inherit">
                                {{-- Product Image --}}
                                <div class="flex-shrink-0 w-24 h-24 mr-6">
                                    <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name ?? 'Product Image' }}" class="w-full h-full object-cover rounded-lg">
                                </div>
                                {{-- Product Info --}}
                                <div class="flex-grow">
                                    <h3 class="text-xl font-semibold text-base-content">{{ $product->name ?? 'Product Name' }}</h3>
                                    <p class="text-lg text-primary font-medium mt-1">
                                        RM {{ number_format($product->price ?? 0, 2) }}
                                    </p>
                                    <p class="text-sm text-base-content/70 mt-1">
                                        Stock: {{ $product->stock_quantity ?? 'N/A' }}
                                    </p>
                                </div>
                                {{-- Fulfilled Orders Count --}}
                                <div class="flex-shrink-0 text-right ml-6 min-w-[60px]">
                                    <p class="text-2xl font-bold text-secondary">{{ $fulfilledQuantity }}</p>
                                    <p class="text-sm text-base-content/70">Sold</p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Pagination Links (if you are using pagination) --}}
                {{-- <div class="mt-8">
                    {{ $products->links() }}
                </div> --}}
            @else
                <div class="text-center py-10">
                    <p class="text-xl text-base-content/70">
                        @if(request('query'))
                            No products found for "{{ request('query') }}".
                        @else
                            No products to display.
                        @endif
                    </p>
                    <p class="text-base-content/50 mt-2">Try adjusting your search terms or browse categories.</p>
                </div>
            @endif
        </div>
    </div>
</x-layout.main>
