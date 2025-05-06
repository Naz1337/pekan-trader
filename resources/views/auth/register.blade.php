<x-layout>
    <div class="flex justify-center">
        <form class="py-4 px-6 bg-base-300 flex flex-col
                     max-w-[400px] justify-center items-center mt-48
                     rounded-box grow-1"
              action="{{ route('register.post') }}"
              method="POST">
            @fragment('form')
                <div class="text-base-content/80 text-5xl mb-16">Register</div>

                @csrf

                <label class="floating-label mb-4" for="name">
                    <span>Name</span>
                    <input type="text" placeholder="Name" name="name" id="name"
                           class="input" required value="{{ old('name') }}">
                </label>

                <label class="floating-label mb-4" for="email">
                    <span>Email</span>
                    <input type="text" placeholder="Email" name="email" id="email"
                           class="input" required value="{{ old('email') }}">
                </label>

                <label class="floating-label mb-6" for="password">
                    <span>Password</span>
                    <input type="password" name="password" id="password" placeholder="Password"
                           class="input" required>
                </label>

                <label class="floating-label mb-6" for="password_confirmation">
                    <span>Password Confirmation</span>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           placeholder="Password Confirmation" class="input" required>
                </label>

                <div class="flex items-center mb-8">
                    <button class="btn btn-primary">Register</button>
                </div>

                <div class="text-base-content/40 mb-8 flex flex-col gap-4">
                    <div>
                        Already have an account? <a href="{{ route('login') }}" class="link link-hover link-secondary">Log In</a>
                    </div>

                    <div>
                        Are you a seller? <a href="{{ route('register.seller') }}"
                                             class="link link-hover link-secondary">Register as a Seller</a>

                    </div>
                </div>

            @endfragment
        </form>
    </div>
</x-layout>
