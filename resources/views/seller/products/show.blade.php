@use('Illuminate\Support\Facades\Storage')
<x-layout.seller title="Product Details">
    <div class="mb-8">
        @if (count($product->all_image_urls) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($product->all_image_urls as $imageUrl)
                    <div>
                        <img src="{{ $imageUrl }}" alt="Image for {{ $product->name }}"
                             class="rounded-box shadow-md object-cover w-full h-40 sm:h-48">
                    </div>
                @endforeach
            </div>
        @else
            {{-- Fallback: Using the main_image_url which has its own placeholder logic --}}
            <img src="{{ $product->main_image_url }}" alt="Product image for {{ $product->name }}"
                 class="rounded-box shadow-md max-w-xs sm:max-w-sm md:max-w-md mb-8 max-h-60 sm:max-h-70">
            <p class="text-sm text-gray-500 italic mt-2">No additional images available, or main image shown.</p>
        @endif
    </div>

    <div class="flex justify-between max-w-150 items-center">
        <x-show.field label="Product Name" :value="$product->name" :is-bold="true"/>
        <div class="flex gap-4" x-data>
            <form action="{{ route('seller.products.destroy', compact('product')) }}" method="post" x-ref="deleteForm"
                  class="hidden">
                @csrf
                @method('DELETE')
            </form>
            <dialog x-ref="deleteModal" class="modal">
                <div class="modal-box">
                    <h3 class="text-lg font-bold">Are you sure?</h3>
                    <div class="py-4">
                        <p class="mb-4">
                            You are about to delete <span class="font-semibold">{{ $product->name }}</span>.
                        </p>
                        <p>Press Yes to continue.</p>
                    </div>
                    <div class="modal-action">
                        <div class="flex gap-4">
                            <button class="btn btn-primary" @@click="$refs.deleteModal.close()">No</button>
                            <button class="btn btn-outline btn-error" @@click="
                            const event = new Event('submit', {
                                bubbles: true,
                                cancelable: true
                            });

                            $refs.deleteForm.dispatchEvent(event)
                            ">Yes</button>
                        </div>
                    </div>
                </div>
                <form method="dialog" class="modal-backdrop">
                    <button>close</button>
                </form>
            </dialog>
            <button class="btn btn-outline btn-error" @@click="$refs.deleteModal.showModal()">Remove</button>
            <a href="{{ route('seller.products.edit', compact('product')) }}" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <x-show.field label="Product Details">
        @if ($product->description === '')
            <div class="italic text-base-content/40">There is no description for this product</div>
        @else
            <div class="max-h-50 text-sm text-base-content/70 overflow-auto rounded-box p-2 bg-base-200 inset-shadow-2xs">
                {!! nl2br(e($product->description)) !!}
            </div>
        @endif
    </x-show.field>
    <x-show.field label="Price" :value="'RM ' . number_format($product->price, 2)" />
    <x-show.field label="Stock" :value="$product->stock_quantity" />
    <x-show.field label="Delivery Fee" :value="'RM ' . number_format($product->delivery_fee, 2)" />
    <x-show.field label="Is Published?">
        <form action="{{ route('seller.products.update', compact('product')) }}" method="post">
            @csrf
            @method('PUT')

            <div>
                <input type="checkbox" name="is_published" id="is_published" class="toggle toggle-primary"
                       @checked($product->is_published)>
            </div>
        </form>
    </x-show.field>
</x-layout.seller>
