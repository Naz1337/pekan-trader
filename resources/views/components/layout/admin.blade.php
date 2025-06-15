@props(['title' => "Pekan Trader Admin"])

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $title }}</title>
    <script src="/scripts/htmx.min.js"></script>
    <link rel="preload" as="image" href="/imgs/user-icon.png">
</head>
<body>
    <div class="navbar bg-base-200/50 shadow-sm justify-center">
        <div class="container flex">
            <div class="flex-1">
                <a class="btn btn-ghost text-accent-content btn-lg" href="{{ route('admin.dashboard') }}">
                    <img class="h-full" src="/imgs/logo-baru.png" alt="Pekan Traders Logo">
                    <span class="ml-2 text-xl font-bold">Admin Dashboard</span>
                </a>
            </div>
            <div class="flex-none flex gap-4 flex-row-reverse items-center">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-ghost">
                        <x-icon.arrow-right-on-rectangle class="size-[1.2em] fill-current" />
                        Logout
                    </button>
                </form>
                <div class="dropdown dropdown-hover dropdown-end">
                    <div tabindex="0" role="button" class="hover:cursor-pointer rounded-full w-10 h-10">
                        <img src="/imgs/user-icon.png" alt="Admin" class="w-full h-full object-cover rounded-full">
                    </div>
                    <div tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-300 rounded-box w-52">
                        <ul class="p-2">
                            <div class="menu-text text-base-content/60">Logged in as <span class="font-semibold">Admin</span></div>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-100 shadow-md">
        <div class="mx-auto py-3 max-w-[60rem]">
            <div class="flex justify-center items-center space-x-4 px-8 w-fit">
                <a href="{{ route('admin.dashboard') }}"
                   @class([
                       'text-sm hover:text-primary whitespace-nowrap px-2 py-1 rounded-md',
                       'bg-primary text-primary-content font-semibold' => request()->routeIs('admin.dashboard')
                   ])>
                    Dashboard
                </a>
                <a href="{{ route('admin.sellers.index') }}"
                   @class([
                       'text-sm hover:text-primary whitespace-nowrap px-2 py-1 rounded-md',
                       'bg-primary text-primary-content font-semibold' => request()->routeIs('admin.sellers.index') || request()->routeIs('admin.sellers.show')
                   ])>
                    All Sellers
                </a>
                <a href="{{ route('admin.sellers.pending') }}"
                   @class([
                       'text-sm hover:text-primary whitespace-nowrap px-2 py-1 rounded-md',
                       'bg-primary text-primary-content font-semibold' => request()->routeIs('admin.sellers.pending')
                   ])>
                    Pending Approvals
                </a>
            </div>
        </div>
    </div>

    <div class="mb-32">
        {{ $slot }}
    </div>

    @session('toast')
        <div class="toast" x-data="{show: true}" x-show="show" x-transition>
        <div class="alert alert-{{ $value['type'] }} relative">
            <button class="absolute top-1 right-1 btn btn-circle btn-{{ $value['type'] }} max-h-[16px] max-w-[16px]"
                    @@click="show = false">X</button>
            <div>{{ $value['message'] }} </div>
        </div>
    </div>
    @endsession

</body>
</html>
