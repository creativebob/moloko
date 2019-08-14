<!doctype html>
<html class="no-js" lang="ru" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">

    <link rel="stylesheet" href="{{ mix('/css/project/app.css') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    {{-- Дополнительные плагины / скрипты / стили для конкретной страницы --}}
    @yield('inhead')
</head>
<body>
    <div id="app">
        <header>
            Роботоет! )
        </header>

        <main>
        @yield('content')
        </main>

        <footer>
        </footer>
    </div>

    <script src="{{ mix('/js/project/app.js') }}"></script>

    @stack('scripts')
</body>
</html>



