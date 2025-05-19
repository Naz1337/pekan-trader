<x-layout.seller title="Orders">
    <div class="flex flex-col gap-4">
        @foreach($orders as $order)
            <a href="{{ route('seller.orders.show', ['order' => $order->id]) }}"
               class="block p-4 bg-base-100 rounded-lg shadow-sm hover:bg-base-200 transition">
                <div class="text-2xl mb-1">
                    <span class="text-base-content/60">Order</span>
                    <span class="text-base-content">{{ $order->id }}</span>
                </div>
                <div class="text-base-content/60 mb-1">
                    RM <span class="text-primary">{{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="text-sm text-base-content/60 mb-8">
                    <span class="text-base-content font-semibold">{{ $order->items->count() }}</span> product types
                </div>
                <div class="text-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="badge
                        {{ $order->status === 'canceled' ? 'badge-error' :
                           ($order->status === 'pending' ? 'badge-warning' :
                           ($order->status === 'packing' ? 'badge-info' :
                           ($order->status === 'delivering' ? 'badge-primary' :
                           ($order->status === 'completed' ? 'badge-success' : 'badge-secondary')))) }}">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                        <span class="badge
                            {{ $order->payment_status === 'paid' ? 'badge-success' :
                               ($order->payment_status === 'unpaid' ? 'badge-error' :
                               ($order->payment_status === 'reupload_required' ? 'badge-warning' : 'badge-secondary')) }}">
                            {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                        </span>
                    </div>
                    <div class="text-right text-base-content/40">
                        {{ $order->created_at->format('d M Y') }}
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-layout.seller>
