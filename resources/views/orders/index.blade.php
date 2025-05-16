<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">
        <h1 class="text-4xl font-bold mb-8">My Orders</h1>
        @forelse ($orders as $order)
            <a href="{{ route('orders.show', $order->id) }}" class="block w-full mb-6 p-6 bg-base-200 rounded-box shadow hover:bg-base-300 transition">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span class="font-semibold">Order #{{ $order->id }}</span>
                        <span class="ml-2 text-sm text-base-content/60">Seller: {{ $order->seller->business_name ?? 'N/A' }}</span>
                    </div>
                    <span class="badge {{ $order->payment_status === 'unpaid' ? 'badge-error' : 'badge-success' }}">
                        {{ ucwords(str_replace('_', ' ', $order->payment_status)) }}
                    </span>
                </div>
                <div class="mb-2">
                    <span class="font-semibold">Items:</span>
                    <div class="flex flex-col gap-3 mt-2">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-4 bg-base-100 rounded-box p-2 shadow-xs">
                                <div class="avatar">
                                    <div class="w-12 h-12 rounded">
                                        <img src="{{ Storage::url($item->product->image_path) }}" alt="{{ $item->product->name }}" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $item->product->name }}</div>
                                    <div class="text-sm text-base-content/60">Quantity: {{ $item->quantity }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-between items-center mt-4">
                    <div>
                        <span class="font-semibold">Total:</span>
                        RM {{ number_format($order->total_amount, 2) }}
                    </div>
                    <div>
                        <span class="font-semibold">Payment:</span>
                        {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center text-lg text-base-content/60">You have no orders yet.</div>
        @endforelse
    </div>
</x-layout.main>
