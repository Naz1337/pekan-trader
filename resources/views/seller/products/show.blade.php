@use('Illuminate\Support\Facades\Storage')
<x-layout.seller title="Product Details">
    <div>
        <img src="{{ Storage::url($product->image_path) }}" alt=""
             class="rounded-box shadow-md max-w-120 mb-8 max-h-70">
    </div>

    <div class="flex justify-between max-w-150">
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
                            <button class="btn btn-secondary" @@click="$refs.deleteModal.close()">No</button>
                            <button class="btn btn-outline btn-primary" @@click="
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
            <button class="btn btn-primary">Edit</button>
        </div>
    </div>

    <x-show.field label="Product Details">
        @if ($product->description === '')
            <div class="italic text-base-content/40">There is no description for this product</div>
        @else
            <div class="max-w-80 max-h-40 text-sm text-base-content/70 overflow-auto shadow-sm rounded-box p-2 bg-base-200">
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
                <input type="checkbox" name="is_published" id="is_published" class="toggle toggle-success"
                       @checked($product->is_published)>
            </div>
        </form>
    </x-show.field>
</x-layout.seller>
