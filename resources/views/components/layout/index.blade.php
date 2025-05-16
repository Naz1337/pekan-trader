@props(['title' => "Pekan Trader"])

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $title }}</title>
    <script src="/scripts/htmx.min.js"></script>
{{--    <script src="https://unpkg.com/htmx-ext-loading-states@2.0.0/loading-states.js"></script>--}}
    <link rel="preload" as="image" href="/imgs/user-icon.png">
</head>
<body>
    <div style="display: contents;" class="">
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
