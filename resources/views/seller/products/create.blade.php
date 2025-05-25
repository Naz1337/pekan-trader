<x-layout.seller title="Product Form">
    <form action="{{ route('seller.products.store') }}" method="post"
          class="max-w-lg flex flex-col gap-4" enctype="multipart/form-data">
        @csrf
        <div class="text-base-content/40">Basic Product Details</div>

        <x-form.input id="product_name" label="Product Name:" />
        <x-form.input id="product_description" label="Product Description:"
                      :textarea="true" class="min-h-32" :required="false"/>
        <x-form.input id="product_price" label="Product Price:" type="slot">
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
                    ">
            </label>
        </x-form.input>
        <x-form.input id="stock_quantity" label="Stock Quantity:" type="slot">
            <input type="number" min="0" id="stock_quantity" name="stock_quantity" class="input" x-bind:value="oldValue"
                   x-data="{oldValue: 0}" @@change="
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
        </x-form.input>

        <div x-data="{
            images: [], // To store { id: uniqueId, file: File, previewUrl: '' }
            handleFiles(event) {
                const newFiles = Array.from(event.target.files);
                newFiles.forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.images.push({
                            id: Date.now() + Math.random(), // unique id for key
                            file: file,
                            previewUrl: e.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                });
            },
            removeImage(id) {
                this.images = this.images.filter(img => img.id !== id);
            }
        }">
            <label for="product_images" class="basis-[200px] text-base-content/60 mt-2">Product Images:</label>
            <input type="file" id="product_images" name="product_images[]" multiple accept="image/*" @@change="handleFiles($event)" class="file-input file-input-bordered w-full max-w-xs" />
            <div class="flex">
                <div class="basis-[200px]"></div>
                <div class="grid grid-cols-3 gap-4 mt-4">
                    <template x-for="image in images" :key="image.id">
                        <div class="relative">
                            <img :src="image.previewUrl" class="rounded shadow object-cover h-32 w-full">
                            <button @@click.prevent="removeImage(image.id)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 text-xs">X</button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="text-base-content/40">Delivery Details</div>
        <x-form.input id="delivery_fee" label="Delivery Fee:" type="slot" class="mb-8">
            <label for="delivery_fee" class="input">
                <span class="label">RM</span>
                <input type="text" class="grow" id="delivery_fee" name="delivery_fee"
                       value="0.00" placeholder="0.00" min="0" step="0.01" x-data="{
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
                    ">
            </label>
        </x-form.input>

        <div class="flex items-stretch mb-8">
            <label for="is_published" class="basis-[200px] flex items-center text-base-content/60">Make Product Public:</label>
            <input type="checkbox" name="is_published" id="is_published" class="toggle toggle-primary">
        </div>

        <div class="flex">
            <div class="basis-[200px]"></div>
            <div class="flex gap-4">
                <button class="btn btn-primary" type="submit">Submit</button>
                <button class="btn btn-ghost" type="reset">Reset</button>
            </div>
        </div>

    </form>
</x-layout.seller>
