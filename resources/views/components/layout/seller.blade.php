@props([
    'title' => 'Page Title'
])

<x-layout.main>
    <div class="w-full flex items-stretch">
        <div class="bg-base-200 me-12 flex flex-col justify-stretch w-55 pt-10 max-h-200 gap-2">
            <x-nav.side-item :url="route('seller.dashboard')">Dashboard</x-nav.side-item>
            <x-nav.side-item :url="route('seller.products.index')">Products</x-nav.side-item>
            <x-nav.side-item url="youtube.com">Dashboard</x-nav.side-item>
            <x-nav.side-item url="yahoo.com">Dashboard</x-nav.side-item>
        </div>
        <div class="pt-10 mb-12 px-4 flex-1 @container">
            <div class="w-full p-4 bg-base-300 @3xl:w-[48rem] min-w-[400px] rounded-box shadow-sm">
                @if ($title !== false)
                    <div class="mb-8">
                        <div class="font-medium text-3xl text-base-content/35">{{ $title }}</div>
                    </div>
                @endif
                {{ $slot }}
            </div>

        </div>
    </div>
</x-layout.main>
