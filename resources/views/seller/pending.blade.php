<x-layout>
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full p-6 bg-base-300 rounded-box shadow-lg">
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4">Account Pending Approval</h2>
                <div class="alert alert-info mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Your seller account is currently pending approval by our administrators.</span>
                </div>
                <p class="text-base-content/70 mb-6">
                    We are reviewing your application. This process typically takes 1-2 business days.
                    You will be notified via email once your account has been approved.
                </p>
                <div class="flex justify-center">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        Return to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
