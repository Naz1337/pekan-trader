@use('Illuminate\Support\Facades\Storage')
<x-layout.seller title="Product List">
    <div class="flex justify-between mb-8">
        <form class="flex gap-8" method="get">
            <label for="search_product" class="floating-label">
                <span>Search</span>
                <input type="text" placeholder="Search Product..." class="input input-md w-60"
                       name="search_product" id="search_product" maxlength="50" value="{{ $search_product }}">
            </label>
            <button class="btn btn-ghost text-base-content/40 hover:text-base-content">Search</button>
        </form>
        <a class="btn btn-soft btn-primary" href="{{ route('seller.products.create') }}">Create</a>
    </div>

    @if($products->count() !== 0)
        <div>
            <div class="text-base-content/40 mb-8">
                Showing {{ $products->count() }} products
            </div>
            <div class="flex flex-wrap gap-4">
                @foreach($products as $product)
                    <a class="card card-side bg-base-100 shadow-sm w-[345px] max-h-[200px]" href="{{ route('seller.products.show', compact('product')) }}">
                        <figure>
                            <img class="w-[120px] max-h-[100px] object-contain p-1" src="{{ Storage::url($product->image_path) }}" />
                        </figure>
                        <div class="card-body">
                            <h2 class="card-title">{{ $product->name }}</h2>
                            <p class="mb-2">RM 9.99</p>

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
