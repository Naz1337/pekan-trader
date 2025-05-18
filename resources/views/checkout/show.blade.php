@use(Illuminate\Support\Facades\Storage)
<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <h1 class="text-4xl font-bold mb-8">Checkout</h1>
        <p class="text-base-content/70 mb-6 text-center">
            Please review your order details below before proceeding to place your order.
        </p>

        @foreach ($groupedProducts as $sellerId => $group)
            <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
                <h2 class="text-2xl font-semibold mb-4"><span class="label">Seller:</span> {{ $group['products']->first()->seller->business_name }}</h2>
                <div class="flex flex-col gap-4">
                    @foreach ($group['products'] as $product)
                        <div class="flex items-center gap-4 p-4 bg-base-300 rounded-box">
                            <img class="w-24 h-24 object-contain" src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}">
                            <div class="flex-1">
                                <div class="text-lg font-semibold">{{ $product->name }}</div>
                                <div class="text-primary">RM {{ number_format($product->price, 2) }}</div>
                                <div class="text-sm text-base-content/60">Quantity: {{ $product->pivot->quantity }}</div>
                            </div>
                            <div class="text-lg font-bold">
                                RM {{ number_format($product->price * $product->pivot->quantity, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 text-right text-xl font-bold">
                    <span class="label">Total for this seller:</span> RM {{ number_format($group['total_amount'], 2) }}
                </div>
            </div>
        @endforeach

        <div class="w-full p-4 bg-base-200 rounded-box mb-6">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold">Grand Total:</div>
                <div class="text-2xl text-primary font-bold">
                    RM {{ number_format($groupedProducts->sum(fn($group) => $group['total_amount']), 2) }}
                </div>
            </div>
        </div>

        <form action="{{ route('order.place') }}" method="post" class="w-full">
            @csrf
            <div x-data="{
    selectedAddressId: 'new_address',
    addresses: {{ Js::from($addresses->keyBy('id')) }},
    recipient_name: '{{ addslashes(request()->user()->name) }}',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'Malaysia',
    phone_number: '',
    remember_address: true,
    isNewAddress: true,
    updateFormFields() {
        this.isNewAddress = (this.selectedAddressId === 'new_address');
        if (this.isNewAddress) {
            this.recipient_name = '{{ addslashes(request()->user()->name) }}';
            this.address_line_1 = '';
            this.address_line_2 = '';
            this.city = '';
            this.state = '';
            this.postal_code = '';
            this.country = 'Malaysia';
            this.phone_number = '';
            this.remember_address = true;
        } else {
            const selectedAddress = this.addresses[this.selectedAddressId];
            if (selectedAddress) {
                this.recipient_name = selectedAddress.recipient_name || '';
                this.address_line_1 = selectedAddress.address_line_1 || '';
                this.address_line_2 = selectedAddress.address_line_2 || '';
                this.city = selectedAddress.city || '';
                this.state = selectedAddress.state || '';
                this.postal_code = selectedAddress.postal_code || '';
                this.country = selectedAddress.country || '';
                this.phone_number = selectedAddress.phone_number || '';
                this.remember_address = selectedAddress.is_default || false;
            }
        }
    }
}"
                 class="w-full p-4 bg-base-200 rounded-box mb-6">
                <h2 class="text-xl font-bold mb-4">Shipping Address</h2>

                <label for="address_selector" class="block font-medium mb-2 label">Select an Address:</label>
                <select id="address_selector" name="address_selector" class="select w-full mb-8"
                        x-model="selectedAddressId"
                        @@change="updateFormFields()"> {{-- Moved @@change here --}}
                    <option value="new_address">Create new Address</option>
                    @foreach ($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->address_line_1 }}, <span class="text-base-content/60">{{ $address->phone_number }}</span>
                        </option>
                    @endforeach
                </select>

                <x-form.input id="recipient_name" label="Recipient Name:" class="mb-4" x-model="recipient_name" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="address_line_1" label="Address Line 1:" class="mb-4" x-model="address_line_1" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="address_line_2" label="Address Line 2:" class="mb-4" :required="false" x-model="address_line_2" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="city" label="City:" class="mb-4" x-model="city" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="state" label="State:" class="mb-4" x-model="state" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="postal_code" label="Postal Code:" class="mb-4" x-model="postal_code" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="country" label="Country:" class="mb-4" x-model="country" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="phone_number" label="Phone Number:" class="mb-4" x-model="phone_number" x-bind:disabled="!isNewAddress"/>
                <x-form.input id="remember_address" label="Remember this address for future use:" type="slot"
                              class="mb-4">
                    <div class="flex h-full flex-col justify-center">
                        <input type="checkbox" id="remember_address" name="remember_address"
                               class="checkbox self-start" x-model="remember_address" x-bind:disabled="!isNewAddress"/>
                        {{-- Removed the @@click from here as x-model handles it, unless the console.log was important for debugging --}}
                    </div>
                </x-form.input>
            </div>
            <div class="w-full p-4 bg-base-200 rounded-box mb-6">
                <h2 class="text-xl font-bold mb-4">Payment Method</h2>
                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Bank Transfer</span>
                        <input type="radio" name="payment_method" value="bank_transfer" class="radio" checked>
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-full">Place Order</button>
        </form>
    </div>
</x-layout.main>
