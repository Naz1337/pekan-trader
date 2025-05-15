<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <div class="flex gap-4 justify-center mb-10">
            <label for="search" class="input input-lg w-120">
                <input type="text" name="search" id="search" class="grow">
                <button class="btn btn-primary"><x-icon.search class="h-[2em]" /></button>
            </label>
        </div>
        <div class="flex flex-col items-center p-4 rounded-box w-220">
            <div class="flex flex-wrap gap-4 justify-start w-[784px]">
                <div class="card bg-base-200 w-46">
                    <figure>
                        <img src="/imgs/mdrop.jpg" alt="" class="w-46 h-46">
                    </figure>
                    <div class="card-body">
                        <h2 class="card-title">Moondrop Space Travel TWS</h2>
                        <div>RM 99.99</div>

                        <div class="divider m-0"></div>

                        <div class="flex gap-2 text-sm items-baseline text-base-content/60">
                            <x-icon.user-circle class="max-h-[1em] shrink-0 relative top-[0.125em]"/>
                            <div>Red Ape Studio KingZ Kpoopers</div>
                        </div>
                    </div>
                </div>
                @foreach($products as $product)
                    <x-catalogue.item :$product />
                @endforeach
            </div>
        </div>


    </div>

</x-layout.main>
