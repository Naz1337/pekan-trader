<x-layout.seller title="Editing Product Details">
    <form action="{{ route('seller.products.update', compact('product')) }}" method="post"
          enctype="multipart/form-data" style="display: contents;">
        <div x-data="{originalImage: null, showReset: false}" class="mb-8"
             x-init="$nextTick(() => {originalImage = $refs.productImage.src;}); ">
            <input type="file" name="product_image" class="hidden" x-ref="fileInputEl" accept="image/*"
                   @@change="
                const files = $event.target.files;

                if (files[0]) {
                    const file = files[0];

                    const fileReader = new FileReader();

                    fileReader.onload = (e) => {
                        $refs.productImage.src = e.target.result;
                    }

                    fileReader.readAsDataURL(file)

                    showReset = true;
                }
                else {
                    $refs.productImage.src = originalImage
                }
            ">
            <div class="relative rounded-box shadow-md w-fit overflow-hidden hover:shadow-xl transition-shadow mb-4">
                <img src="{{ Storage::url($product->image_path) }}" alt=""
                     class="max-w-120 max-h-70" x-ref="productImage">
                <div class="absolute top-0 left-0 w-full h-full hover:backdrop-blur-sm transition-[backdrop-filter] group">
                    <button class="w-full h-full text-base-100 text-4xl text-shadow-lg btn bg-transparent
                transition-opacity opacity-0 group-hover:opacity-100" @@click.prevent="
                $refs.fileInputEl.click();
                ">Edit Image</button>
                </div>
            </div>
            <button class="btn" @@click="
            $event.preventDefault();

            $refs.productImage.src = originalImage;
            $refs.fileInputEl.value = '';

            showReset = false;

        " x-show="showReset" x-cloak>Reset Image</button>
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

        <div class="flex gap-4" x-data>
            <button class="btn" @@click.prevent="window.history.back()">Back</button>
            <button class="btn btn-primary" type="submit">Save</button>
        </div>

        @csrf
        @method('PUT')
    </form>

</x-layout.seller>
