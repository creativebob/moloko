@php

$session_god = session('god');
$session_access = session('access');

$user_filial_id = $session_access['user_info']['filial_id'];
$user_status = $session_access['user_info']['user_status'];
$company_id = $session_access['user_info']['company_id'];
$company_name = $session_access['company_info']['company_name'];
// $company_designation = $session_access['company_info']['company_designation'];

if (isset($session_access['user_info']['position_id'])) {
    $position_id = $session_access['user_info']['position_id'];
} else {
    $position_id = null;
}


$rights_user_filial = collect($session_access['all_rights'])->keys()->implode('\n');

if(isset($session_access['list_authors']['authors_id'])){$count_authors = ' +' . count($session_access['list_authors']['authors_id']);} else {$count_authors = "";};

@endphp

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
{{--    <script type="application/javascript" src="{{ mix('/js/system/app.js') }}"></script>--}}

    {{-- Transition --}}
    <style type="text/css">
        .title-bar {
            display: none;
        }
    </style>
    <title>@yield('title')</title>

    {{-- Дополнительные плагины / скрипты / стили для конкретной страницы --}}
    @yield('inhead')

    {{-- Подключаем класс Checkboxer --}}
    @include('includes.scripts.class.checkboxer')

</head>

{{-- Блочим все подергивания в блоке  --}}
<body id="body" class="block-refresh">
    <div id="app">

        {{-- Хедер --}}
        <div class="top-bar-container header-z-index" id="header" data-sticky-container>
            <div class="sticky sticky-topbar" data-sticky data-margin-top="0" data-options="stickyOn: small;" data-top-anchor="header:top">
                <header class="grid-x header">
                    <div class="small-7 left-head cell">
{{--                         Кнопка сворачивания на мобилках--}}
                        <div class="title-bar" data-responsive-toggle="sidebar" data-hide-for="medium" data-hide-for="large">
                            <button class="menu-icon" type="button" data-toggle="sidebar"></button>
                             <div class="title-bar-title"></div>
                        </div>
{{--                         Логотип--}}
                        <h1><span>CRM</span>System</h1>
                    </div>
                    <div class="small-5 right-head cell">
                        <ul>
                         <li>
                            @if(isset($session_god))
                            {{ link_to_route('users.returngod', 'Вернуться к богу', $value = Null) }}
                            @endif
                        </li>

                        @can('index', App\Challenge::class)
                            <li>
                                <a id="task-toggle"><img src="/img/system/header/alert.png">
                                    @if(!empty($list_challenges['for_me']))
                                        <span class="challenges_count" id="challenges-count">{{ $list_challenges['for_me']->flatten()->count() }}</span>
                                    @endif
                                </a>
                            </li>
                        @endcan
                            <li>
                                <a data-toggle="profile">

                                    <span>
                                        @if(isset(Auth::user()->company_id))
                                        {{ $company_designation ?? $company_name }}  |
                                        @endif
                                        {{ isset(Auth::user()->login) ? Auth::user()->login : 'Чужак' }} {{ $count_authors }}
                                    </span>

{{--                                    <img src="{{ getPhotoPath(Auth::user(), 'small') }}" alt="Аватар" class="avatar">--}}
                                </a>
                            </li>
                        </ul>
                        <div class="dropdown-pane profile-head" id="profile" data-dropdown data-position="bottom" data-alignment="right" data-v-offset="10" data-h-offset="-30" data-close-on-click="true">
                            <ul class="menu vertical">
                                <li>
                                    <a href="{{ route('users.profile') }}">Мой профиль</a>
{{--                                    {{ link_to_route('users.myprofile', 'Мой профиль', $value = Null) }}--}}
                                </li>
{{--                                 <li><a href="">Настройки</a></li>--}}
{{--                                <li><hr></li>--}}
{{--                                 <li><a href="">Нужна помощь?</a></li>--}}
                                <li>
                                    @if(isset($company_id)&&($user_status == 1))
                                    {{ link_to_route('users.getgod', 'Выйти из компании', $value = Null) }}
                                    @endif
                                </li>
                                <li>
                                    @if(isset($session_god))
                                    {{ link_to_route('users.returngod', 'Вернуться к богу', $value = Null) }}
                                    @endif
                                </li>
                                <li>
                                    @if(isset($session_access))
                                    {{ link_to_route('help.show_session', 'Смотреть сессию', $value = Null, ['target' => '_blank']) }}
                                @endif</li>

{{--                                 Кнопка выхода--}}
                                <li><a href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">Выход</a>
                                </li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>

                            </ul>
                        </div>
                    </div>
                </header>
            </div>
        </div>

        @php
        $session = session('conditions');

        if ($session) {

            $setting_sidebar = $session['conditions']['sidebar'];
            if (isset($setting_sidebar)) {
                if ($session['conditions']['sidebar'] == 'open') {
                    $sidebar = 'sidebar-open';
                } else {
                    $sidebar = '';
                }
            } else {
                $sidebar = '';
            }

            $setting_task = $session['conditions']['task'];
            if (isset($setting_task)) {
                if ($session['conditions']['task'] == 'open') {
                    $task = 'task-open';
                } else {
                    $task = '';
                }
            } else {
                $task = '';
            }

        } else {
            $sidebar = 'sidebar-open';
            $task = '';
        }

        @endphp

{{--         Основной сайдбар, весь функционал--}}
        @include('layouts.sidebar', ['open' => $sidebar])


{{--         Менеджер задач--}}
        @if(Auth::user())
            @include('layouts.task-manager', ['open' => $task])
        @endif


        {{-- Основной контент --}}
        <div id="wrapper">

            <div class="grid-x breadcrumbs block-refresh">
                <div class="small-12 medium-9 cell">
{{--                     Breadcrumbs--}}
                    @yield('breadcrumbs')
                </div>
                <div class="small-12 medium-3 cell text-right" id="extra-panel">

{{--                     Planfact--}}
                    @yield('planfact')

{{--                     Exсel--}}
                    @yield('exсel')

                </div>
            </div>

            <div class="grid-x">

                <div class="small-12 cell">
                @if (session('success'))

                            <div class="alert alert-success" role="alert">
                                <div class="alert callout" data-closable>
                                {{ session()->get('success') }}
                                <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                            </div>

                @endif
                </div>

                <div class="small-12 cell errors">

{{--                     Блок ошибок--}}
                    @if ($errors->any())
                    <div class="alert callout" data-closable>
                        <h5>Ошибки ввода данных:</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid-x">
                <main class="content small-12">

                    {{-- Прилипающий заголовок --}}
                    @yield('title-content')

{{--                    --}}{{-- Функционал --}}
                    @yield('control-content')

                    {{-- Основой контент --}}
                    @yield('content')

                </main>
            </div>

            {{-- Модальные окна --}}
            @yield('modals')
        </div>
        {{-- Footer --}}
{{--        <footer class="grid-x footer">--}}
{{--            <div class="small-12 cell">--}}
{{--                <ul class="right">--}}
{{--                    <li>КОЛ-ВО ЗАЯВОК С САЙТА: <span>12</span></li>--}}
{{--                    <li>КОЛ-ВО ЗВОНКОВ: <span>9</span></li>--}}
{{--                    <li>РЕКЛАМАЦИИ: <span>0</span></li>--}}
{{--                    <li>ПОСТУПЛЕНИЯ ДЕНЕГ: <span>670 500</span></li>--}}
{{--                    <li class="foot-drop" id="foot-drop"><a class="icon-footer sprite" data-toggle="foot-options"></a></li>--}}
{{--                </ul>--}}
{{--                <div class="dropdown-pane foot-options" id="foot-options" data-dropdown data-position="top" data-alignment="right" data-v-offset="6" data-h-offset="-32" data-close-on-click="true">--}}
{{--                    <ul class="menu vertical checkbox">--}}
{{--                        <li>--}}
{{--                            <input type="checkbox" name="" id="leads-option">--}}
{{--                            <label for="leads-option"><span>Количество заявок</span></label>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <input type="checkbox" name="" id="calls-option">--}}
{{--                            <label for="calls-option"><span>Количество звонков</span></label>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <input type="checkbox" name="" id="claim-option">--}}
{{--                            <label for="claim-option"><span>Рекламации</span></label>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <input type="checkbox" name="" id="money-option">--}}
{{--                            <label for="money-option"><span>Поступления денег</span></label>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </footer>--}}
    </div>

    {{-- Скрипты --}}
    <script type="application/javascript" src="{{ mix('/js/system/app.js') }}"></script>

    {{-- Наши скрипты --}}
    @stack('scripts')

    @yield('scripts')

</body>
</html>
