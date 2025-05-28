<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <div class="flex flex-col items-center p-4 rounded-box w-220">
            <div class="flex flex-wrap gap-4 justify-start w-[784px]">
                @foreach($products as $product)
                    <x-catalogue.item :$product />
                @endforeach
            </div>
        </div>


    </div>

</x-layout.main>
