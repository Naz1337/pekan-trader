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
            <div class="w-full p-4 bg-base-200 rounded-box mb-6">
                <h2 class="text-xl font-bold mb-4">Shipping Address</h2>

                <label for="address_selector" class="block font-medium mb-2 label">Select an Address:</label>
                <select id="address_selector" name="address_selector" class="select w-full mb-8">
                    <option selected value="new_address">Create new Address</option>
                    @foreach ($addresses as $address)
                        <option value="{{ $address->id }}">
                            {{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->state }}, {{ $address->postal_code }}
                        </option>
                    @endforeach
                </select>

                <x-form.input id="recipient_name" label="Recipient Name:" class="mb-4" :value="request()->user()->name"/>
                <x-form.input id="address_line_1" label="Address Line 1:" class="mb-4" />
                <x-form.input id="address_line_2" label="Address Line 2:" class="mb-4" :required="false"/>
                <x-form.input id="city" label="City:" class="mb-4"  />
                <x-form.input id="state" label="State:" class="mb-4"  />
                <x-form.input id="postal_code" label="Postal Code:" class="mb-4"  />
                <x-form.input id="country" label="Country:" class="mb-4" value="Malaysia"/>
                <x-form.input id="phone_number" label="Phone Number:" class="mb-4"  />
                <x-form.input id="remember_address" label="Remember this address for future use:" type="slot"
                              class="mb-4">
                    <div class="flex h-full flex-col justify-center">
                        <input type="checkbox" id="remember_address" name="remember_address"
                               class="checkbox self-start"/>
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
