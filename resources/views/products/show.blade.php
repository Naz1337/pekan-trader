@use(Illuminate\Support\Facades\Storage)
<x-layout.main>
    <div x-data="{}" class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">

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

                <div class="p-6 bg-base-200 rounded-box mt-4">
                    <div class="mb-2 flex items-center gap-2">
                        <x-icon.store class="w-4 h-4 shrink-0 text-base-content/80"/>
                        <strong>Seller:</strong> <a href="{{ route('seller.profile.show', $product->seller) }}" class="link link-hover">{{ $product->seller->business_name }}</a>
                    </div>
                    <div class="flex gap-2 mb-4">
                        @auth
                            <button x-data type="button" class="btn btn-sm btn-soft"
                                    @click.stop="
                                        console.log('Chat button clicked for seller ID:', {{ $product->seller->id }});
                                        if (window.ChatController?.component) {
                                            console.log('ChatController found, opening chat');
                                            window.ChatController.component.openChatWithSeller({{ $product->seller->id }});
                                        } else {
                                            console.log('ChatController not found, dispatching event');
                                            window.dispatchEvent(new CustomEvent('open-chat-with-seller', { detail: { sellerId: {{ $product->seller->id }} } }));
                                        }
                                    ">
                                <x-icon.paper-plane class="size-[1.2em]"/>
                                Chat
                            </button>
                        @else
                            <a class="btn btn-sm btn-soft" href="{{ route('login') }}">
                                <x-icon.paper-plane class="size-[1.2em]"/>
                                Chat
                            </a>
                        @endauth

                        {{-- Follow functionality with Alpine.js --}}
                        @auth
                            <div x-data="{
                                isFollowing: @json(auth()->user()->isFollowing($product->seller)),
                                isLoading: false,
                                error: null,

                                async toggleFollow() {
                                    this.isLoading = true;
                                    this.error = null;

                                    try {
                                        let response;
                                        if (this.isFollowing) {
                                            response = await fetch('{{ route('seller.unfollow', $product->seller) }}', {
                                                method: 'DELETE',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            });
                                        } else {
                                            response = await fetch('{{ route('seller.follow', $product->seller) }}', {
                                                method: 'POST',
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                    'Content-Type': 'application/json'
                                                }
                                            });
                                        }

                                        const data = await response.json();

                                        if (response.ok) {
                                            this.isFollowing = !this.isFollowing;
                                        } else {
                                            this.error = data.error || data.message || 'An error occurred';
                                        }
                                    } catch (err) {
                                        this.error = 'Network error occurred';
                                        console.error('Follow error:', err);
                                    } finally {
                                        this.isLoading = false;
                                    }
                                }
                            }">
                                <button
                                    @click="toggleFollow"
                                    :disabled="isLoading"
                                    class="btn btn-sm btn-soft"
                                    :class="{
                                        'opacity-50': isLoading
                                    }"
                                >
                                    <template x-if="isLoading">
                                        <span class="loading loading-spinner loading-xs"></span>
                                    </template>

                                    <template x-if="!isLoading && isFollowing">
                                        <x-icon.user-minus class="size-[1.2em]"/>
                                    </template>

                                    <template x-if="!isLoading && !isFollowing">
                                        <x-icon.user-plus class="size-[1.2em]"/>
                                    </template>

                                    <span x-text="isLoading ? 'Loading...' : (isFollowing ? 'Unfollow' : 'Follow')"></span>
                                </button>

                                <div x-show="error" x-transition class="text-error text-sm mt-1" x-text="error"></div>
                            </div>
                        @else
                            <a class="btn btn-sm btn-soft" href="{{ route('login') }}">
                                <x-icon.user-plus class="size-[1.2em]"/>
                                Follow
                            </a>
                        @endauth
                    </div>
                </div>

                <form class="p-6 bg-base-200 rounded-box mt-4"
                      action="{{ route('catalogue.add_to_cart', compact('product')) }}"
                      method="post">
                    <div class="mb-8">
                        @if($product->stock_quantity <= 0)
                            <span class="badge badge-error badge-lg font-medium">Out of Stock</span>
                        @elseif($product->stock_quantity > 0 && $product->stock_quantity <= 10)
                            <span class="badge badge-warning badge-lg font-medium">Low Stock ({{ $product->stock_quantity }} left)</span>
                        @else
                            <span class="badge badge-success badge-lg font-medium">In Stock ({{ $product->stock_quantity }} available)</span>
                        @endif
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
                <div class="flex items-center mb-4">
                    <div class="text-6xl">{{ $product->name }}</div>
                    @if($product->created_at->gt(now()->subDays(7)))
                        <span class="badge badge-accent badge-lg font-semibold ml-4">NEW</span>
                    @endif
                </div>
                <div class="text-5xl mb-4 text-primary">RM {{ number_format($product->price, 2) }}</div>

                {{-- Product Category --}}
                @if($product->category)
                    <div class="text-md text-base-content/70 mb-4 flex items-center">
                        <x-icon.tag class="w-4 h-4 mr-2 text-primary"/>
                        <span>{{ $product->category->name }}</span>
                    </div>
                @endif

                {{-- Fulfilled Orders Count --}}
                @php
                    $fulfilledQuantity = 0;
                    if (method_exists($product, 'orderItems')) {
                        $fulfilledQuantity = $product->orderItems()
                            ->whereHas('order', function ($query) {
                                $query->where('status', 'completed');
                            })
                            ->sum('quantity');
                    }
                @endphp
                @if($fulfilledQuantity > 0)
                    <div class="mb-8 flex items-center gap-2">
                        <x-icon.star class="w-5 h-5 text-yellow-500"/>
                        <span class="font-semibold text-lg text-base-content/90">{{ $fulfilledQuantity }}</span>
                        <span class="text-md text-base-content/70">sold</span>
                    </div>
                @endif

                <div>
                    @if ($product->description)
                        {!! nl2br(e($product->description)) !!}
                    @else
                        <div class="text-base-content/40 italic">There is no description for this product</div>
                    @endif
                </div>

                {{-- Product Specifications Section --}}
                @if ($product->attributes && $product->attributes->count() > 0)
                    <div class="mt-8 pt-6 border-t border-base-300">
                        <h3 class="text-2xl font-semibold mb-6">Product Specifications</h3>
                        <div class="space-y-3">
                            @foreach ($product->attributes as $attribute)
                                <div class="flex">
                                    <span class="font-semibold text-base-content w-2/5 md:w-1/3 shrink-0 pr-2">{{ $attribute->productAttributeKey->display_name }}:</span>
                                    <span class="text-base-content/80">{{ $attribute->value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-layout.main>
