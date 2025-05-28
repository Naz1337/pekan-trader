@use(Illuminate\Support\Facades\Storage)
<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">

        <div class="flex items-start w-full">
            <div class="flex flex-col gap-16 min-w-[425px]">
                <div x-data='{
                        images: @json($product->all_image_urls),
                        currentImage: @json($product->main_image_url),
                        changeImage(newUrl) {
                            this.currentImage = newUrl;
                        }
                    }'
                     class="flex flex-col gap-4">

                    {{-- Main Image Display --}}
                    <div class="w-full">
                        <img :src="currentImage" alt="{{ $product->name }} image" class="w-full max-w-lg min-h-100 object-contain rounded-box mx-auto">
                    </div>

                    {{-- Thumbnail Gallery --}}
                    <template x-if="images.length > 1">
                        <div class="flex flex-row gap-2 p-2 overflow-x-auto w-full mt-2">
                            <template x-for="(imageUrl, index) in images" :key="index">
                                <img :src="imageUrl"
                                     @click="changeImage(imageUrl)"
                                     alt="Product thumbnail {{ '${index + 1}' }}"
                                     class="w-24 h-24 object-cover rounded cursor-pointer border-2 flex-shrink-0"
                                     :class="{ 'border-primary': imageUrl === currentImage, 'border-transparent': imageUrl !== currentImage }"
                                >
                            </template>
                        </div>
                    </template>
                </div>

                <form class="p-6 bg-base-200 rounded-box mt-4"
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

                @php
//                xdebug_break();
                @endphp

                {{-- NEW: Product Specifications Section --}}
                @if ($product->attributes && $product->attributes->count() > 0)
                    <div class="mt-8 pt-6 border-t border-base-300"> {{-- Add margin, padding, and a top border for separation --}}
                        <h3 class="text-2xl font-semibold mb-6">Product Specifications</h3> {{-- Section title --}}
                        <div class="space-y-3"> {{-- Tailwind class for vertical spacing between attribute items --}}
                            @foreach ($product->attributes as $attribute)
                                <div class="flex">
                                    <span class="font-semibold text-base-content w-2/5 md:w-1/3 shrink-0 pr-2">{{ $attribute->productAttributeKey->display_name }}:</span> {{-- Attribute Key: bolded, specific width, right padding --}}
                                    <span class="text-base-content/80">{{ $attribute->value }}</span> {{-- Attribute Value --}}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- End of NEW Product Specifications Section --}}
            </div>
        </div>

    </div>
</x-layout.main>
