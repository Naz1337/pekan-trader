<x-layout.admin>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">All Sellers</h1>

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Business Name</th>
                        <th>Owner Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Date Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sellers as $seller)
                        <tr>
                            <td>{{ $seller['business_name'] }}</td>
                            <td>{{ $seller['user_name'] }}</td>
                            <td>{{ $seller['email'] }}</td>
                            <td>{{ $seller['status'] }}</td>
                            <td>{{ $seller['created_at'] }}</td>
                            <td>
                                <a href="{{ route('admin.sellers.show', $seller['id']) }}" class="btn btn-sm btn-primary">View Details</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout.admin>
