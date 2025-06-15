<x-layout>
    <div class="p-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Admin Dashboard</h1>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="btn btn-ghost">Logout</button>
            </form>
        </div>

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
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('admin.sellers.index') }}" class="btn btn-primary">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
