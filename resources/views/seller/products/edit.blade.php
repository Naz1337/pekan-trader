<x-layout.seller title="Editing Product Details">
    <form action="{{ route('seller.products.update', compact('product')) }}" method="post"
          enctype="multipart/form-data" style="display: contents;">
        <div x-data="{
            newImages: [], // To store { id: uniqueId, file: File, previewUrl: '' }
            handleFiles(event) {
                const files = Array.from(event.target.files);
                files.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.newImages.push({
                            id: Date.now() + Math.random(), // unique id for key
                            file: file,
                            previewUrl: e.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                });
            },
            removeNewImage(id) {
                this.newImages = this.newImages.filter(img => img.id !== id);
            }
        }" class="mb-8">
            <div class="text-base-content/40 mb-2">Current Product Images</div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                @forelse($product->images as $image)
                    <div class="relative">
                        <img src="{{ Storage::url($image->image_path) }}" alt="Product Image" class="rounded shadow object-cover h-32 w-full">
                        @if($image->is_thumbnail)
                            <span class="absolute top-1 left-1 badge badge-primary">Thumbnail</span>
                        @endif
                        {{-- For now, no direct deletion of existing images from here. New uploads will replace. --}}
                    </div>
                @empty
                    <p>No images currently uploaded for this product.</p>
                @endforelse
            </div>

            <label for="product_images" class="basis-[200px] text-base-content/60 mt-2">Upload New Images (will replace existing):</label>
            <input type="file" id="product_images" name="product_images[]" multiple accept="image/*" @@change="handleFiles($event)" class="file-input file-input-bordered w-full max-w-xs" />
            <div class="flex">
                <div class="basis-[200px]"></div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <template x-for="image in newImages" :key="image.id">
                        <div class="relative">
                            <img :src="image.previewUrl" class="rounded shadow object-cover h-32 w-full">
                            <button @@click.prevent="removeNewImage(image.id)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs">X</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="flex justify-between max-w-150 items-center">
            <x-show.field label="Product Name" :is-bold="true">
                <input type="text" class="input" value="{{ $product->name }}" name="product_name">
            </x-show.field>
            <div class="flex gap-4" x-data>
                <button class="btn" @@click.prevent="window.history.back()">Back</button>
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
        </div>

        <x-show.field label="Product Category">
            <select name="product_category_id" id="product_category_id" class="select select-bordered w-full">
                @foreach($productCategories as $category)
                    <option value="{{ $category->id }}" @selected($product->product_category_id == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </x-show.field>

        <x-show.field label="Product Details">
        <textarea class="min-h-50 w-full textarea overflow-auto inset-shadow-2xs text-base-content/80"
                  name="product_description"
                  placeholder="Enter your product description...">{{ $product->description }}</textarea>
        </x-show.field>

        <x-show.field label="Price">
            <label for="product_price" class="input">
                <span class="label">RM</span>
                <input type="text" class="grow" id="product_price" name="product_price"
                       placeholder="0.00" min="0" step="0.01" x-data="{
                        oldValue: ''
                    }"
                       @@input="
                       const permissiveRegex = /^-?\d*\.?\d{0,2}$/;

                       // Allow only valid partial input
                       if (permissiveRegex.test($el.value)) {
                           oldValue = $el.value; // keep updating as user types, but only valid partials
                       } else {
                           $el.value = oldValue; // revert on invalid input
                       }
                    "
                       @@blur="
                   const strictRegex = /^-?\d+(\.\d{1,2})?$/;

                   const value = $el.value;

                   // If empty, just return or handle as needed
                   if (value === '') {
                     oldValue = '';
                     return;
                   }

                   // If valid, format to 2 decimal places
                   if (strictRegex.test(value)) {
                     const num = parseFloat(value);
                     if (!isNaN(num)) {
                       $el.value = num.toFixed(2);
                       oldValue = $el.value;
                     }
                   } else {
                     // If invalid on blur, revert to last valid value
                     $el.value = oldValue;
                   }
                    "
                       value="{{ number_format($product->price, 2) }}">
            </label>
        </x-show.field>
        <x-show.field label="Stock">
            <input type="number" min="0" id="stock_quantity" name="stock_quantity" class="input" x-bind:value="oldValue"
                   x-data="{oldValue: @json($product->stock_quantity)}" @@change="
                    const result = cleanNumberString($el.value);
                    console.log(result)
                    if (result === null) {
                        console.log('hello?');
                        $el.value = oldValue;
                    }
                    else {
                        $el.value = result
                        oldValue = $el.value
                    }
                   ">
        </x-show.field>


        <x-show.field label="Delivery Fee">
            <label for="product_price" class="input">
                <span class="label">RM</span>
                <input type="text" class="grow" id="product_price" name="delivery_fee"
                       placeholder="0.00" min="0" step="0.01" x-data="{
                        oldValue: ''
                    }"
                       @@input="
                       const permissiveRegex = /^-?\d*\.?\d{0,2}$/;

                       // Allow only valid partial input
                       if (permissiveRegex.test($el.value)) {
                           oldValue = $el.value; // keep updating as user types, but only valid partials
                       } else {
                           $el.value = oldValue; // revert on invalid input
                       }
                    "
                       @@blur="
                   const strictRegex = /^-?\d+(\.\d{1,2})?$/;

                   const value = $el.value;

                   // If empty, just return or handle as needed
                   if (value === '') {
                     oldValue = '';
                     return;
                   }

                   // If valid, format to 2 decimal places
                   if (strictRegex.test(value)) {
                     const num = parseFloat(value);
                     if (!isNaN(num)) {
                       $el.value = num.toFixed(2);
                       oldValue = $el.value;
                     }
                   } else {
                     // If invalid on blur, revert to last valid value
                     $el.value = oldValue;
                   }
                    "
                       value="{{number_format($product->delivery_fee, 2) }}">
            </label>
        </x-show.field>


        <x-show.field label="Is Published?">
            <div>
                <input type="checkbox" name="is_published" id="is_published" class="toggle toggle-success"
                    @checked($product->is_published)>
            </div>
        </x-show.field>

        <div class="text-base-content/40 mt-8 mb-4">Product Attributes</div>

        @if($product->productAttributes->isEmpty())
            <p class="text-sm text-base-content/60 mb-4">This product has no attributes yet. Add an attribute type below to get started.</p>
        @else
            <div class="overflow-x-auto mb-4">
                <table class="table w-full">
                    <thead>
                    <tr>
                        <th>Attribute Name</th>
                        <th>Value</th>
                        <th>Order</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($product->productAttributes->sortBy('order_column') as $index => $attribute)
                        <tr>
                            <td>{{ $attribute->productAttributeKey->display_name }}</td>
                            <td>
                                <input type="hidden" name="attributes[{{ $index }}][id]" value="{{ $attribute->id }}">
                                <input type="text" name="attributes[{{ $index }}][value]" value="{{ $attribute->value }}" class="input input-bordered input-sm w-full max-w-xs">
                            </td>
                            <td>
                                <input type="number" name="attributes[{{ $index }}][order_column]" value="{{ $attribute->order_column }}" class="input input-bordered input-sm w-20">
                            </td>
                            <td>
                                <form action="{{ route('seller.products.attributes.destroy', ['product' => $product->id, 'attribute' => $attribute->id]) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-error btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex gap-4 mt-8" x-data>
            <button class="btn" @@click.prevent="window.history.back()">Back</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>

        @csrf
        @method('PUT')
    </form>

    <div class="mt-4">
        <h3 class="text-base-content/60 mb-2">Add New Attribute Type</h3>
        <form action="{{ route('seller.products.attributes.store', $product->id) }}" method="post" class="flex gap-2">
            @csrf
            <input type="text" name="attribute_type_name" placeholder="e.g., Color, Material" class="input input-bordered grow">
            <button type="submit" class="btn btn-primary">Add Attribute Type</button>
        </form>
    </div>

</x-layout.seller>
