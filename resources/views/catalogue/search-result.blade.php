<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        {{-- Search Bar (similar to welcome.blade.php) --}}
        <div class="flex gap-4 justify-center mb-10 w-full max-w-lg">
            {{-- Assuming the search is handled by GET request or JavaScript --}}
            <form action="{{ route('home') }}" method="GET" class="w-full"> {{-- Example route --}}
                <label for="search" class="input input-bordered input-lg flex items-center gap-2 w-full">
                    <input type="text" name="query" id="search" class="grow" placeholder="Search products..." value="{{ request('query') }}">
                    <button type="submit" class="btn btn-primary btn-square">
                        <x-icon.search class="h-6 w-6" />
                    </button>
                </label>
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
            </form>
        </div>

        {{-- Display current search query and category --}}
        <div class="w-full max-w-3xl mb-4 text-center">
            @if(isset($query) || isset($categoryName))
                <h2 class="text-2xl font-bold text-base-content">
                    Search Results
                    @if(isset($query) && $query)
                        for "{{ $query }}"
                    @endif
                    @if(isset($categoryName) && $categoryName)
                        in "{{ $categoryName }}"
                    @endif
                </h2>
            @endif
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
                            <a href="{{ route('catalogue.show', $product->id) }}" class="flex items-center bg-base-200 p-4 rounded-xl shadow-md hover:bg-base-300 transition-colors duration-150 ease-in-out no-underline text-inherit group">
                                {{-- Product Image --}}
                                <div class="flex-shrink-0 w-24 h-24 mr-6 relative">
                                    {{-- "New" Badge --}}
                                    @if($product->created_at->gt(now()->subDays(7)))
                                        <div class="badge badge-accent badge-sm absolute top-1 right-1 z-10 font-semibold">NEW</div>
                                    @endif

                                    <img src="{{ $product->main_image_url }}" alt="{{ $product->name ?? 'Product Image' }}"
                                         class="w-full h-full object-cover rounded-lg group-hover:scale-105 transition-transform duration-300 ease-in-out">

                                    {{-- Stock Indicator (Overlay on image or below) --}}
                                    @if($product->stock_quantity <= 0)
                                        <div class="absolute bottom-1 left-1/2 -translate-x-1/2 z-10">
                                            <span class="badge badge-error badge-outline font-medium">Out of Stock</span>
                                        </div>
                                    @elseif($product->stock_quantity > 0 && $product->stock_quantity <= 10)
                                        <div class="absolute bottom-1 right-1 z-10">
                                            <span class="badge badge-warning badge-outline badge-sm font-medium">Low Stock</span>
                                        </div>
                                    @endif
                                </div>
                                {{-- Product Info --}}
                                <div class="flex-grow flex flex-col justify-between">
                                    <div>
                                        {{-- Category --}}
                                        @if($product->category)
                                            <div class="text-xs text-base-content/70 mb-1.5 flex items-center">
                                                <x-icon.tag class="w-3 h-3 mr-1.5 text-primary"/>
                                                <span>{{ $product->category->name }}</span>
                                            </div>
                                        @endif

                                        <h3 class="text-xl font-semibold text-base-content mb-1 leading-snug hover:text-primary transition-colors">
                                            {{ $product->name ?? 'Product Name' }}
                                        </h3>

                                        <p class="text-lg text-primary font-medium mb-2">
                                            RM {{ number_format($product->price ?? 0, 2) }}
                                        </p>

                                        {{-- Short Description --}}
                                        <p class="text-sm text-base-content/80 mb-3 h-10 overflow-hidden">
                                            {{ Str::limit($product->description, 70) }}
                                        </p>
                                    </div>

                                    @if($product->seller)
                                        <div class="mt-auto pt-2 border-t border-base-300">
                                            <div class="flex gap-2 text-xs items-center text-base-content/70">
                                                <x-icon.store class="w-3.5 h-3.5 shrink-0 text-base-content/80"/>
                                                <span class="truncate" title="{{ $product->seller->business_name }}">
                                                    {{ $product->seller->business_name }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                {{-- Fulfilled Orders Count --}}
                                <div class="flex-shrink-0 text-right ml-6 min-w-[60px]">
                                    @if($fulfilledQuantity > 0)
                                        <div class="flex flex-col items-end">
                                            <div class="flex items-center gap-1.5">
                                                <x-icon.star class="w-4 h-4 text-yellow-500"/>
                                                <span class="font-semibold text-sm text-base-content/90">{{ $fulfilledQuantity }}</span>
                                            </div>
                                            <span class="text-xs text-base-content/70">sold</span>
                                        </div>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- Pagination Links --}}
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-10">
                    <p class="text-xl text-base-content/70">
                        @if(isset($query) && $query)
                            No products found for "{{ $query }}".
                        @elseif(isset($categoryName) && $categoryName)
                            No products found in category "{{ $categoryName }}".
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
