
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">

        <link rel="stylesheet" href="{{ mix('/css/system/app.min.css') }}">

        {{-- CSRF Token --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Add jQuery library --}}
        <script type="application/javascript" src="/js/system/jquery.latest.min.js"></script>

        <title>@yield('title')</title>

        {{-- Дополнительные плагины / скрипты / стили для конкретной страницы --}}
        @yield('inhead')

        <style>
            .robot-message{
                padding: 1rem 0rem 1rem 2rem;
                font-size: 1.3rem;
            }

            .robot-message-block{
                margin-top: 20%;
            }

            .wrap-img-robot{
                border-right: 2px solid #ddd;
            }

            .img-robot{
                margin-right: 2rem;
            }

        </style>

    </head>

    {{-- Блочим все подергивания в блоке  --}}
    <body id="body" class="block-refresh">
        <div id="app">
            <div class="grid-x">
                <div class="cell small-12 medium-10 large-6 large-offset-2 robot-message-block">
                    <div class="grid-x">
                        <div class="cell small-4 medium-6 text-right wrap-img-robot">
                            <img src="/img/system/robot.svg" width="90px" title="Robot system" class="img-robot">
                        </div>
                        <div class="cell small-6 medium-6 ">
                            <p class="robot-message">{{ $exception->getMessage() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
