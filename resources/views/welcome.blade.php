<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
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
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>


    </div>

</x-layout.main>
