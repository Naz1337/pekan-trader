<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <h1 class="text-4xl font-bold mb-8">Order Details</h1>

        <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
            <div class="mb-2">
                <span class="font-semibold">Order ID:</span> {{ $order->id }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">Status:</span>
                <span class="badge {{ $order->payment_status === 'unpaid' ? 'badge-error' : 'badge-success' }}">
                    {{ ucwords(str_replace('_', ' ', $order->payment_status)) }}
                </span>
            </div>
            <div class="mb-2">
                <span class="font-semibold">Payment Method:</span> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">Total Amount:</span> RM {{ number_format($order->total_amount, 2) }}
            </div>
        </div>

        <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
            <h2 class="text-2xl font-semibold mb-4">Items</h2>
            <div class="flex flex-col gap-4">
                @foreach ($order->items as $item)
                    <div class="flex items-center gap-4 p-4 bg-base-300 rounded-box">
                        <img class="w-24 h-24 object-contain" src="{{ Storage::url($item->product->image_path) }}" alt="{{ $item->product->name }}">
                        <div class="flex-1">
                            <div class="text-lg font-semibold">{{ $item->product->name }}</div>
                            <div class="text-primary">RM {{ number_format($item->product->price, 2) }}</div>
                            <div class="text-sm text-base-content/60">Quantity: {{ $item->quantity }}</div>
                        </div>
                        <div class="text-lg font-bold">
                            RM {{ number_format($item->product->price * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($order->payment_status === 'unpaid')
            <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
                <h2 class="text-xl font-bold mb-4">Bank Transfer Payment</h2>
                <div class="mb-4">
                    <div class="font-semibold">Bank Name:</div>
                    <div>{{ $order->seller->bank_name }}</div>
                </div>
                <div class="mb-4">
                    <div class="font-semibold">Account Number:</div>
                    <div>{{ $order->seller->bank_account_number }}</div>
                </div>
                <div class="mb-4">
                    <div class="font-semibold">Account Holder:</div>
                    <div>{{ $order->seller->bank_account_name }}</div>
                </div>
                <form action="{{ route('order.pay', $order->id) }}" method="post" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    <div>
                        <label class="font-semibold mb-2 block">Upload Bank Transfer Receipt</label>
                        <input type="file" name="receipt" accept="image/*,application/pdf" class="file-input file-input-bordered w-full" required>
                        @error('receipt')
                        <div class="text-error text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" value="{{ $order->payment_method }}" name="method">
                    <button type="submit" class="btn btn-primary w-full">Submit Payment</button>
                </form>
            </div>
        @else
            @if ($order->order_payment && $order->order_payment->receipt_path)
                <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
                    <h2 class="text-xl font-bold mb-4">Payment Receipt</h2>
                    <a href="{{ Storage::url($order->order_payment->receipt_path) }}" class="btn btn-secondary" download>
                        Download Receipt
                    </a>
                </div>
            @endif
        @endif
    </div>
</x-layout.main>
