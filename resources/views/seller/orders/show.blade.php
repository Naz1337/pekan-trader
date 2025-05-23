<x-layout.seller title="Order Details">
    <div class="max-w-280 ms-auto me-auto rounded-box flex flex-col items-center p-4">
        <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
            <div class="mb-2">
                <span class="font-semibold">Order ID:</span> {{ $order->id }}
            </div>
            <div class="mb-2">
                <span class="font-semibold">Order Status:</span>
                <span class="badge {{ $order->status === 'pending' ? 'badge-warning' : ($order->status === 'packing' ? 'badge-info' : 'badge-success') }}">
                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div class="mb-2">
                <span class="font-semibold">Payment Status:</span>
                <span class="badge {{ $order->payment_status === 'unpaid' ? 'badge-error' : ($order->payment_status === 'reupload_required' ? 'badge-warning' : 'badge-success') }}">
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

        <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
            <h2 class="text-2xl font-semibold mb-4">Delivery Details</h2>
            @if ($order->address)
                <div class="mb-2">
                    <span class="font-semibold">Recipient Name:</span> {{ $order->address->recipient_name ?? 'N/A' }}
                </div>
                <div class="mb-2">
                    <span class="font-semibold">Address:</span>
                    <div>
                        {{ $order->address->address_line_1 ?? 'N/A' }}<br>
                        {{ $order->address->address_line_2 ? $order->address->address_line_2 . '<br>' : '' }}
                        {{ $order->address->city ?? 'N/A' }}, {{ $order->address->state ?? 'N/A' }}<br>
                        {{ $order->address->postal_code ?? 'N/A' }}, {{ $order->address->country ?? 'N/A' }}
                    </div>
                </div>
                <div class="mb-2">
                    <span class="font-semibold">Phone Number:</span> {{ $order->address->phone_number ?? 'N/A' }}
                </div>
            @else
                <p class="text-base-content/60">No delivery details available.</p>
            @endif
        </div>

        @if ($order->payment_status === 'in_payment' && $order->order_payments->isNotEmpty())
            <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
                <h2 class="text-xl font-bold mb-4">Uploaded Payment Receipts</h2>
                <p class="mb-4">The customer has uploaded multiple receipts for the payment. Please review each one.</p>
                <div class="flex flex-col gap-4">
                    @foreach ($order->order_payments as $payment)
                        <div class="p-4 bg-base-300 rounded-box">
                            <div class="flex justify-between items-center">
                                <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="btn btn-secondary">
                                    View Receipt
                                </a>
                                <span class="badge {{ $payment->status === 'rejected' ? 'badge-error' : ($payment->status === 'accepted' ? 'badge-success' : 'badge-warning') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                                @if ($payment->status === 'pending')
                                    <div class="flex gap-2">
                                        <form action="{{ route('seller.order_payments.accept', $payment->id) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">Accept</button>
                                        </form>
                                        <form action="{{ route('seller.order_payments.reject', $payment->id) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-error">Reject</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                            <div class="text-sm text-base-content/60 mt-2">
                                Uploaded on: {{ $payment->created_at->format('F j, Y, g:i A') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
            <h2 class="text-2xl font-semibold mb-4">Actions</h2>
            @if (($order->payment_status === 'unpaid' || $order->payment_status === 'reupload_required') && $order->status !== 'canceled')
                <form action="{{ route('seller.orders.cancel', $order->id) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-error w-full">Cancel Order</button>
                </form>
            @elseif ($order->status === 'packing')
                <form action="{{ route('seller.orders.setDelivering', $order->id) }}" method="post" class="flex flex-col gap-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="tracking_id" class="font-semibold mb-2 block">Tracking ID</label>
                        <input type="text" name="tracking_id" id="tracking_id" class="input input-bordered w-full" required>
                        @error('tracking_id')
                        <div class="text-error text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Set as Delivering</button>
                </form>
            @else
                <p class="text-base-content/60">There are no further actions required for this order.</p>
            @endif
        </div>

        <div class="w-full mb-6 p-4 bg-base-200 rounded-box">
            <h2 class="text-2xl font-semibold mb-4">Order History</h2>
            @if ($order->order_histories->isEmpty())
                <p class="text-base-content/60">No history available for this order.</p>
            @else
                <ul class="flex flex-col gap-4">
                    @foreach ($order->order_histories as $history)
                        <li class="p-4 {{ $loop->first ? 'bg-base-300' : 'bg-base-300/50' }}  rounded-box {{ $loop->first ? 'shadow-sm' : '' }}">
                            <div class="text-lg font-semibold">{{ $history->message }}</div>
                            <div class="text-sm text-base-content/60">
                                {{ $history->created_at->format('F j, Y, g:i A') }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-layout.seller>
