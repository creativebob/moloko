@php

  $session_god = session('god');
  $session_access = session('access');

  $user_filial_id = $session_access['user_info']['filial_id'];
  $user_status = $session_access['user_info']['user_status'];
  $company_id = $session_access['user_info']['company_id'];
  $company_name = $session_access['company_info']['company_name'];

  $rights_user_filial = collect($session_access['all_rights'])->keys()->implode('\n');
  
  if(isset($session_access['list_authors']['authors_id'])){$count_authors = ' +' . count($session_access['list_authors']['authors_id']);} else {$count_authors = "";};

@endphp

<!doctype html>
<html class="no-js" lang="ru" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Add jQuery library --}}
    <!-- <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> -->
    <script type="text/javascript" src="/js/jquery.latest.min.js"></script>

    {{-- Дополнительные плагины/скрипиты/стили для конкретной страницы --}}
    @yield('inhead')

    <link rel="stylesheet" href="{{ asset('/css/foundation.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Transition --}}
    <style type="text/css">
    .title-bar {
      display: none;
    }
    </style>
    <title>@yield('title')</title>
  </head>
  {{-- Блочим все подергивания в блоке  --}}
  <body id="body" class="block-refresh"> 
    {{-- Хедер --}}
    <div class="top-bar-container header-z-index" id="header" data-sticky-container>
      <div class="sticky sticky-topbar" data-sticky data-margin-top="0" data-options="stickyOn: small;" data-top-anchor="header:top">
        <header class="grid-x header">
          <div class="small-7 left-head cell">
            {{-- Кнопка сворачивания на мобилках --}}
            <div class="title-bar" data-responsive-toggle="sidebar" data-hide-for="medium" data-hide-for="large">
              <button class="menu-icon" type="button" data-toggle="sidebar"></button>
              {{-- <div class="title-bar-title"></div> --}}
            </div>
            {{-- Логотип --}}
            <h1><span>Mars</span>Crm</h1>
          </div>
          <div class="small-5 right-head cell">
            <ul>
              <li>
                  @if(isset($session_god))
                    {{ link_to_route('users.returngod', 'Вернуться к богу', $value = Null) }} 
                  @endif
                </li>
              <li><a id="task-toggle"><img src="/img/header/alert.png"></a></li>
              <li>
                <a data-toggle="profile">
                <span>
                  @if(isset(Auth::user()->company_id))
                    {{ $company_name }}  | 
                  @endif
                  {{ isset(Auth::user()->login) ? Auth::user()->login : 'Чужак' }} {{ $count_authors }}</span><img src="{{ isset(Auth::user()->photo_id) ? '/storage/'.Auth::user()->company_id.'/media/albums/'.Auth::user()->login.'/img/'.Auth::user()->avatar->name : '/storage/icon-pig.png' }}" alt="" class="avatar">
                </a>
              </li>
            </ul>
            <div class="dropdown-pane profile-head" id="profile" data-dropdown data-position="bottom" data-alignment="right" data-v-offset="10" data-h-offset="-30" data-close-on-click="true">
              <ul class="menu vertical">
                <li><a href="index.php">Профиль</a></li>
                <li><a href="">Настройки</a></li>
                <li><hr></li>
                <li><a href="">Нужна помощь?</a></li>
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

                {{-- Кнопка выхода --}}
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
    {{-- Основной сайдбар, весь функционал --}}
    @include('layouts.sidebar')
    {{-- Менеджер задач --}}
    @include('layouts.task-manager')
    {{-- Основной контент --}}
    <div id="wrapper">
      <div class="grid-x breadcrumbs block-refresh">
        <div class="small-12 medium-7 cell"> 
          {{-- Breadcrumbs --}}
          @yield('breadcrumbs')
        </div>
        <div class="small-12 medium-5 cell"> 
          {{-- Breadcrumbs --}}
          @yield('exel')
        </div>
      </div>
      {{-- Контент --}}
      <main class="content">
        {{-- Прилипающий заголовок --}}
        @yield('title-content')
        {{-- Основой контент --}}
        @yield('content')
      </main>
        {{-- Модальные окна --}}
        @yield('modals')
    </div>
    {{-- Footer --}}
    <footer class="grid-x footer">
      <div class="small-12 cell"> 
        <ul class="right">
          <li>КОЛ-ВО ЗАЯВОК С САЙТА: <span>12</span></li>
          <li>КОЛ-ВО ЗВОНКОВ: <span>9</span></li>
          <li>РЕКЛАМАЦИИ: <span>0</span></li>
          <li>ПОСТУПЛЕНИЯ ДЕНЕГ: <span>670 500</span></li>
          <li class="foot-drop" id="foot-drop"><a class="icon-footer sprite" data-toggle="foot-options"></a></li>
        </ul>
        <div class="dropdown-pane foot-options" id="foot-options" data-dropdown data-position="top" data-alignment="right" data-v-offset="6" data-h-offset="-32" data-close-on-click="true">
          <ul class="menu vertical checkbox">
            <li>
              <input type="checkbox" name="" id="leads-option">
              <label for="leads-option"><span>Количество заявок</span></label>
            </li>
            <li>
              <input type="checkbox" name="" id="calls-option">
              <label for="calls-option"><span>Количество звонков</span></label>
            </li>
            <li>
              <input type="checkbox" name="" id="claim-option">
              <label for="claim-option"><span>Рекламации</span></label>
            </li>
            <li>
              <input type="checkbox" name="" id="money-option">
              <label for="money-option"><span>Поступления денег</span></label>
            </li>
          </ul>
        </div>
      </div>
    </footer>
    {{-- Скрипты --}}
    <script src="/js/vendor/what-input.js"></script>
    <script src="/js/vendor/foundation.js"></script>
    <script src="/js/app.js"></script>
    <!-- Наши скрипты -->
    <script type="text/javascript">
    $(function() {
    console.log('Начало обработки страницы');
    });
    $(window).on('load', function () {
      $("body").removeClass("block-refresh");
      renderContent ();
      setTimeout(function(){
        $('#wrapper').css({'transition': 'margin 0.3s ease'});
        $('#sidebar').css({'transition': 'width 0.3s ease'});
        $('#task-manager').css({'transition': 'margin-right 0.3s ease'});
        if ($("div").is("#head-content")) {
          $('.head-content').css({'transition': 'width 0.3s ease'});
        };
        if ($("table").is("#table-content")) {
          // $('#thead-sticky').css({'transition': 'margin 0.1s ease'});
          $('#thead-content').css({'transition': 'width 0.3s ease'});
          $('#thead-content>th').css({'transition': 'width 0.3s ease'});
        };
        // $('#filters').css({'transition': 'height 1s ease'});
        $('.td-drop').width(32);
        $('.td-checkbox').width(32);
        $('.td-delete').width(32);
        getMassWidth ();
        fixedThead ();
        // alert('lol');
      },1);
    });
    $(window).resize(function() {
      renderContent ();
    });
    // Иконка в футере при клике
    // $('.icon-footer').bind('click', function() {
    //   $('#foot-drop').toggleClass('active-foot-drop');
    // });
    </script>
    {{-- Наши скрипты --}}
    @yield('scripts')
  </body>
</html>