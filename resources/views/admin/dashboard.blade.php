<x-layout.admin>
    <div class="p-8">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Pending Sellers Card -->
            <div class="card bg-base-200 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Pending Sellers</h2>
                    <p class="text-4xl font-bold">{{ $pendingCount }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.sellers.pending') }}" class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>

            <!-- All Sellers Card -->
            <div class="card bg-base-200 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">All Sellers</h2>
                    <p class="text-4xl font-bold">{{ $allSellersCount }}</p>
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.sellers.index') }}" class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin>
