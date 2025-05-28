@use('Illuminate\Support\Facades\Storage')
<x-layout.seller title="Product List">
    <div class="flex justify-between mb-8">
        <form class="flex gap-4" method="get" x-data="{searchText: '{{ $search_product }}', showClear: false }"
              x-effect="showClear = searchText !== ''">
            <label for="search_product" class="floating-label">
                <span>Search</span>
                <input type="text" placeholder="Search Product..." class="input input-md w-60"
                       name="search_product" id="search_product" maxlength="50" x-model="searchText">
            </label>
            <button class="btn btn-ghost text-base-content/40 hover:text-base-content"
                    x-ref="searchButton">Search</button>
            <a class="btn" x-show="showClear" x-cloak x-transition.duration.200ms
               href="{{ route('seller.products.index') }}" @@click.prevent="
                searchText = '';
                $nextTick(() => {
                    $refs.searchButton.click();
                });
               ">Clear</a>
        </form>
        <a class="btn btn-soft btn-primary" href="{{ route('seller.products.create') }}">Add New Product</a>
    </div>

    @if($products->count() !== 0)
        <div>
            <div class="text-base-content/40 mb-8">
                Showing {{ $products->count() }} products
            </div>
            <div class="flex flex-wrap gap-4">
                @foreach($products as $product)
                    <a class="card card-side bg-base-100 shadow-sm w-[345px] max-h-[200px]" href="{{ route('seller.products.show', compact('product')) }}">
                        <figure class="border-r-1 border-base-content/10">
                            <img class="w-[120px] max-h-[100px] object-contain p-1" src="{{ Storage::url($product->image_path) }}" />
                        </figure>
                        <div class="card-body">
                            <div class="flex justify-between items-center">
                                <h2 class="card-title">{{ $product->name }}</h2>
                            </div>

                            @if ($product->is_published)
                                <div class="badge badge-soft badge-success">Published</div>
                            @else
                                <div class="badge badge-soft badge-secondary">Not Published</div>
                            @endif

                            <p class="mb-2">RM {{ number_format($product->price, 2) }}</p>

                            <p class="text-secondary justify-self-end"><span class="font-semibold">{{ $product->stock_quantity }}</span> left in stock</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-base-content/24 font-semibold">
            @if (is_null($search_product))
                There is no products yet... go add one!
            @else
                There is no product found :(
            @endif
        </div>
    @endif
</x-layout.seller>
