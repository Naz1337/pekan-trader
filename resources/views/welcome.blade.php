<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        {{-- Add this section for categories --}}
        <div class="w-full mb-6">
            <h2 class="text-2xl font-semibold mb-4 text-center">Categories</h2>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('home') }}"
                   class="btn btn-outline btn-sm {{ (is_null($categoryName) && is_null($query)) ? 'btn-active' : '' }}">
                   All Products
                </a>
                @isset($categories)
                    @forelse($categories as $category)
                        <a href="{{ route('home', ['category' => $category->name]) }}"
                           class="btn btn-outline btn-sm {{ (isset($categoryName) && $categoryName == $category->name) ? 'btn-active' : '' }}">
                            {{ $category->name }}
                        </a>
                    @empty
                        <p class="text-gray-500">No categories available.</p>
                    @endforelse
                @else
                    <p class="text-gray-500">Categories not loaded.</p>
                @endisset
            </div>
        </div>
        {{-- End of category section --}}

        <div class="flex flex-col items-center p-4 rounded-box w-220">
            <div class="flex flex-wrap gap-4 justify-start w-[784px]">
                @if($products->isEmpty())
                    <p class="text-center text-gray-500 w-full">No products found.</p>
                @else
                    @foreach($products as $product)
                        <x-catalogue.item :$product />
                    @endforeach
                @endif
            </div>
            {{-- Removed the pagination links as per user request --}}
        </div>
    </div>

</x-layout.main>
