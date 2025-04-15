<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <!-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> -->
    @yield('style')
    <title>@yield('title')</title>
</head>
<body>
    <div class="content">
        @yield('content')
    </div>
</body>
</html>