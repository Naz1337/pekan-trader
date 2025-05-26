@props([
    'is_logged_in' => auth()->check(),
    'user' => auth()->check() ? request()->user() : null,
    'is_user_a_seller' => request()->user()?->seller()->exists() ?? false
])

<x-layout>
    <div class="navbar bg-base-200/50 shadow-sm justify-center">
        <div class="container flex">
            <div class="flex-1">
                <a class="btn btn-ghost text-accent-content btn-lg" href="{{ route('home') }}">
                    <img class="h-full" src="/imgs/logo.png" alt="Pekan Traders Logo">
                </a>
            </div>
            <div class="flex-none flex gap-4 flex-row-reverse items-center">
                @if ($is_logged_in)
                    <form action="{{ route('logout') }}" method="post" class="max-w-[40px] max-h-[40px]">
                        @csrf
                        <div class="dropdown dropdown-hover dropdown-end">
                            <div tabindex="0" role="button" class="hover:cursor-pointer rounded-full">
                                <img src="/imgs/user-icon.png" alt="User">
                            </div>
                            <div tabindex="0" class="menu dropdown-content">
                                <ul class="bg-base-300 rounded-box z-1 w-52 p-2 shadow-sm">
                                    <div class="menu-text text-base-content/60">Logged in as <span class="font-semibold">{{ $user->name }}</span></div>
                                    <li><a href="">Profile</a></li>
                                    <div class="divider menu-text m-0"></div>
                                    <li><button type="submit" class="hover:bg-error/40 hover:text-error-content">Logout</button></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">
                        Log In
                    </a>
                @endif

                @if ($is_user_a_seller)
                        <a href="/seller/dashboard"
                        @class([
                            'btn',
                            'btn-ghost' => !str_starts_with(request()->path(), 'seller'),
                            'btn-soft btn-secondary' => str_starts_with(request()->path(), 'seller')
                        ])
                        >

                            <x-icon.store class="size-[1.2em] fill-current" />
                            Merchant Dashboard</a>
                @endif

                {{-- Search Bar --}}
                <form class="flex" method="get" action="{{ route('home') }}"> {{-- Submits to homepage, adjust if you have a specific search route --}}
                    <label for="header-search" class="input input-bordered input-md flex items-center gap-2 rounded-lg">
                        <input type="text" name="query" id="header-search" class="grow focus:outline-none" placeholder="Search products...">
                        <button class="btn btn-ghost btn-sm p-1" type="submit">
                            <x-icon.search class="h-5 w-5" />
                        </button>
                    </label>
                </form>

                @if ($is_logged_in)
                    <a href="{{ route('cart.show') }}" class="btn btn-ghost">
                        <x-icon.cart class="size-[1.2em] fill-primary-base-content"/>
                        Cart
                    </a>

                    <a href="{{ route('orders.index') }}" class="btn btn-ghost">
                        <x-icon.clipboard-list class="size-[1.2em] fill-primary-base-content"/>
                        My Order
                    </a>
                @endif
            </div>
        </div>

    </div>

    <div class="mb-32">
        {{ $slot }}
    </div>
</x-layout>
