<x-layout.main>
    <div class="w-fit ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <div class="flex flex-col items-center p-4 rounded-box">
            <div class="flex flex-wrap gap-4 justify-start 2xl:w-[1504px]">
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
