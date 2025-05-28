@props(['seller'])

<x-layout.main :title="$seller->business_name ?? $seller->user->name">
    <div class="container mx-auto py-8">
        <div class="bg-base-200 p-6 rounded-lg shadow-md mb-8">
            <div class="flex items-center space-x-6">
                <img src="{{ $seller->profile_image_url }}" alt="{{ $seller->business_name ?? $seller->user->name }} Profile" class="w-32 h-32 rounded-full object-cover border-4 border-primary">
                <div>
                    <h1 class="text-4xl font-bold text-base-content">{{ $seller->business_name ?? $seller->user->name }}</h1>
                    @if ($seller->business_name)
                        <p class="text-xl text-base-content/70">Operated by {{ $seller->user->name }}</p>
                    @endif
                    <p class="text-base-content/60 mt-2">{{ $seller->business_description ?? 'No description provided.' }}</p>
                    <p class="text-sm text-base-content/50 mt-1">Member Since: {{ $seller->created_at->format('F Y') }}</p>
                </div>
            </div>
        </div>

        <h2 class="text-3xl font-bold text-base-content mb-6">Products by {{ $seller->business_name ?? $seller->user->name }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($seller->products as $product)
                <x-catalogue.item :product="$product" />
            @empty
                <p class="text-base-content/60 col-span-full">This seller has no products listed yet.</p>
            @endforelse
        </div>
    </div>
</x-layout.main>
