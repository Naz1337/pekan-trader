@use(Illuminate\Support\Facades\Storage)
<x-layout.main>
    <div class="max-w-280 ms-auto me-auto mt-20 rounded-box flex flex-col items-center p-4">

        <h1 class="text-4xl font-bold mb-8">Your Cart</h1>

        @if ($products->isEmpty())
            <div class="text-center text-base-content/60 italic">Your cart is empty.</div>
        @else
            <div class="flex flex-col gap-6">
                @foreach ($products as $product)
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
                        <form action="{{ route('cart.remove', $product->id) }}" method="post" class="ml-4">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-error btn-sm">Remove</button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 p-4 bg-base-300 rounded-box">
                <div class="flex justify-between items-center">
                    <div class="text-2xl font-bold">Total:</div>
                    <div class="text-2xl text-primary font-bold">
                        RM {{ number_format($products->sum(fn($product) => $product->price * $product->pivot->quantity), 2) }}
                    </div>
                </div>
                <form action="{{ route('checkout.show') }}" method="post" class="mt-4">
                    @csrf
                    <button class="btn btn-primary w-full">Checkout</button>
                </form>
            </div>
        @endif

    </div>
</x-layout.main>
