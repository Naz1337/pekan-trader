<x-layout>
    <div class="flex justify-center">
        <form class="py-4 px-6 bg-base-300 flex flex-col
                     max-w-[400px] justify-center items-center mt-48
                     rounded-box grow-1"
              hx-post="{{ route('login.post') }}"
              hx-target="this"
              hx-swap="innerHTML"
              hx-disabled-elt="find button, find #email, find #password">
            @fragment('form')

            <div class="mb-4">
                @session('success')
                <div role="alert" class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $value }}</span>
                </div>
                @endsession
            </div>

            <div class="text-base-content/80 text-5xl mb-16">Log In</div>

            @csrf

            <label class="floating-label mb-4" for="email">
                <span>Email</span>
                <input type="text" placeholder="Email" name="email" id="email"
                       class="input" required value="{{ session('email') }}">
            </label>

            <label class="floating-label mb-6" for="password">
                <span>Password</span>
                <input type="password" name="password" id="password" placeholder="Password"
                       class="input" required>
            </label>

            @php $lol = 'cant see this' @endphp
            <div class="text-error mb-8 @error('login') opacity-100 @php $lol = $message @endphp @else opacity-0 @enderror">{{ $lol }}</div>

            <div class="flex items-center mb-8">
                <button class="btn btn-primary">Log In</button>
            </div>

            <div class="text-base-content/40 mb-8">
                Don't have an account? <a href="{{ route('register') }}" class="link link-hover link-secondary">Register</a>
            </div>
            @endfragment
        </form>
    </div>
</x-layout>
