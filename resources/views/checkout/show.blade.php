@use(Illuminate\Support\Facades\Storage)
<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <h1 class="text-4xl font-bold mb-8">Checkout</h1>

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
