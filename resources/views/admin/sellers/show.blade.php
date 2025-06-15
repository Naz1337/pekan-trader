<x-layout.admin>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">{{ $seller['business_name'] }}</h1>

        <div class="flex gap-4 mb-6">
            @if($seller['status'] === 'Pending')
                <form action="{{ route('admin.sellers.approve', $seller['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl mb-6">
                    <div class="card-body">
                        <h2 class="card-title">Owner Information</h2>
                        <p><strong>Name:</strong> {{ $seller['user_name'] }}</p>
                        <p><strong>Email:</strong> {{ $seller['user_email'] }}</p>
                        <p><strong>Status:</strong> {{ $seller['status'] }}</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div role="tablist" class="tabs tabs-boxed">
                    <input type="radio" name="seller_details_tabs" role="tab" class="tab" aria-label="Business Profile" checked />
                    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                        <h3 class="text-xl font-semibold mb-4">Business Profile</h3>
                        <div class="flex items-center mb-4">
                            <img src="{{ $seller['logo_url'] }}" alt="Business Logo" class="w-24 h-24 object-cover rounded-full mr-4">
                            <div>
                                <p><strong>Business Name:</strong> {{ $seller['business_name'] }}</p>
                                <p><strong>Description:</strong> {{ $seller['business_description'] }}</p>
                            </div>
                        </div>
                        <p><strong>Address:</strong> {{ $seller['business_address'] }}</p>
                        <p><strong>Phone:</strong> {{ $seller['business_phone'] }}</p>
                        <p><strong>Email:</strong> {{ $seller['business_email'] }}</p>
                        <p><strong>Operating Hours:</strong> {{ $seller['opening_hour'] }} - {{ $seller['closing_hour'] }}</p>
                        @if($seller['facebook'])
                            <p><strong>Facebook:</strong> <a href="{{ $seller['facebook'] }}" target="_blank" class="link link-primary">{{ $seller['facebook'] }}</a></p>
                        @endif
                        @if($seller['instagram'])
                            <p><strong>Instagram:</strong> <a href="{{ $seller['instagram'] }}" target="_blank" class="link link-primary">{{ $seller['instagram'] }}</a></p>
                        @endif
                    </div>

                    <input type="radio" name="seller_details_tabs" role="tab" class="tab" aria-label="Owner & Legal" />
                    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                        <h3 class="text-xl font-semibold mb-4">Owner & Legal</h3>
                        <p><strong>IC Number:</strong> {{ $seller['ic_number'] }}</p>
                        <p><strong>Business Certificate:</strong> <a href="{{ $seller['business_cert_url'] }}" target="_blank" class="link link-primary">View Certificate</a></p>
                    </div>

                    <input type="radio" name="seller_details_tabs" role="tab" class="tab" aria-label="Bank Details" />
                    <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                        <h3 class="text-xl font-semibold mb-4">Bank Details</h3>
                        <p><strong>Bank Name:</strong> {{ $seller['bank_name'] }}</p>
                        <p><strong>Account Name:</strong> {{ $seller['bank_account_name'] }}</p>
                        <p><strong>Account Number:</strong> {{ $seller['bank_account_number'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.admin>
