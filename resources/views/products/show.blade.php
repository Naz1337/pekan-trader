@use(Illuminate\Support\Facades\Storage)
<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">

        <div class="flex items-start w-full">
            <div class="flex flex-col gap-16">
                <img class="max-w-100 min-h-100 object-contain" src="{{ Storage::url($product->image_path) }}" />
                <form class="p-6 bg-base-200 rounded-box"
                      action="{{ route('catalogue.add_to_cart', compact('product')) }}"
                      method="post">

                    <div class="mb-4">
                        <strong>Seller:</strong> <span class="link">{{ $product->seller->business_name }}</span>
                    </div>
                    <div class="mb-8">
                        <strong>Stock Available:</strong> {{ $product->stock_quantity }}
                    </div>

                    @if ($product->stock_quantity > 0)
                        <x-form.input id="quantity" label="Quantity:" type="number" value="1" class="mb-8"
                                      min="1" max="{{ $product->stock_quantity }}"/>
                        <button class="btn btn-primary w-full">
                            <x-icon.cart-plus class="size-[1.2em] fill-primary-content"/>
                            Add to Cart
                        </button>
                    @else
                        <div class="text-base-content/60 italic mb-8">Out of stock</div>
                        <button class="btn btn-primary w-full" disabled>
                            <x-icon.cart-plus class="size-[1.2em] fill-primary-content"/>
                            Add to Cart
                        </button>
                    @endif
                    @csrf
                </form>
            </div>


            <div class="grow p-4 h-full">
                <div class="text-6xl mb-4">{{ $product->name }}</div>
                <div class="text-5xl mb-16 text-primary">RM {{ number_format($product->price, 2) }}</div>
                <div>
                    @if ($product->description)
                        {!! nl2br(e($product->description)) !!}
                    @else
                        <div class="text-base-content/40 italic">There is no description for this product</div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</x-layout.main>
