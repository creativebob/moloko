<!doctype html>
<html class="no-js" lang="ru" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('/project/css/foundation.css') }}">
  <link rel="stylesheet" href="{{ asset('/project/css/app.css') }}">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  {{-- Add jQuery library --}}
  <!-- <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script> -->
  <script type="text/javascript" src="/project/js/jquery.latest.min.js"></script>
  {{-- Плагины и дополнения --}}
  @yield('inhead')
  <title>@yield('title')</title>
</head>
<body>
  {{-- Хедер --}}
  <header class="grid-x align-center header">
    <div class="small-10 medium-10 large-5 cell">

      <div class="media-object">
        <div class="media-object-section">
          <a href="/">
            <img class="logo" src="/project/img/logo-project.png" alt="Лого">
          </a>
        </div>
        <div class="media-object-section">
          <h1>{{ $department->name }}</h1>
          <address>{{ $department->location->city->name . ', ' . $department->location->address }}</address>
        </div>
      </div>
      
    </div>

    <div class="small-8 medium-10 large-5 cell">
      <ul class="horizontal menu staff-list grid-x grid-margin-x small-up-1 medium-up-2">
        @foreach ($graphics as $graphic)
        <li class="cell">
          <div class="staffer">{{ $graphic['user']['first_name'] . ' ' . $graphic['user']['patronymic'] . ' ' . $graphic['user']['second_name'] }}</div>
          <a class="phone" href="tel:{{ callPhone($graphic['user']['phone']) }}">{{ decorPhone($graphic['user']['phone']) }}</a>
          <div data-toggle="worktimes-{{ $graphic['id'] }}">
            <a class="worktime-open">Сегодня:</a>
            <span class="worktimes"> 
            {{ isset($graphic['schedule']['days'][date('N')]) ? $graphic['schedule']['days'][date('N')]['worktime_begin'] . ' - ' . $graphic['schedule']['days'][date('N')]['worktime_end'] : 'Выходной' }}</span>
          </div>
          <div class="dropdown-pane worktimes-pane" id="worktimes-{{ $graphic['id'] }}" data-dropdown data-close-on-click="true" data-position="bottom" data-alignment="center">
            <table class="worktimes-table">
              <caption class="text-center">График работы:</caption>
              <tbody>
                @for ($x = 1; $x <= 7; $x++)
                <tr @if($x == date('N')) {{ 'class=day-now' }} @endif>
                  <td>@switch ($x)
                    @case (1) пн. @break
                    @case (2) вт. @break
                    @case (3) ср. @break
                    @case (4) чт. @break
                    @case (5) пт. @break
                    @case (6) сб. @break
                    @case (7) вс. @break
                    @endswitch
                  </td>
                  <td>{{ isset($graphic['schedule']['days'][$x]) ? $graphic['schedule']['days'][$x]['worktime_begin'] . ' - ' . $graphic['schedule']['days'][$x]['worktime_end'] : 'Выходной' }}</td>
                </tr>
                @endfor
              </tbody>
            </table>
          </div>
        </li>
        @endforeach
        {{-- <li class="cell">
          <span class="staffer">Вита Беломестнова</span>
          <a class="phone" href="tel:+79041248598">8 (904) 124-85-98</a>
          <a class="worktime-open" data-toggle="worktimes-2">Сегодня:</a><span class="worktimes">9:00 - 17:00</span>

          <div class="dropdown-pane worktimes-pane" id="worktimes-2" data-dropdown data-close-on-click="true">
            <table class="worktimes-table">
              <caption class="text-center">График работы:</caption>
              <tbody>
                @for ($x = 1; $x <= 7; $x++)
                <tr @if($x == date('N')) class="day-now" @endif>
                  <td class="text-center">
                    @switch ($x)
                    @case (1) пн. @break
                    @case (2) вт. @break
                    @case (3) ср. @break
                    @case (4) чт. @break
                    @case (5) пт. @break
                    @case (6) сб. @break
                    @case (7) вс. @break
                    @endswitch
                  </td>
                  <td>{{ $grafic2[$x] }}</td>
                </tr>
                @endfor
              </tbody>
            </table>
          </div>

        </li> --}}
      </ul>
    </div>
  </header>
  <div class="grid-x align-center">
    <nav class="small-10 cell navigation">
      @if(!empty($navigations['main']))
      <ul class="menu menu-hover-lines">
        @foreach ($navigations['main']->menus as $menu)
        @if (empty($menu->alias))
        @php
        $link = $menu->page->alias;
        @endphp
        @else
        @php
        $link = $menu->alias;
        @endphp
        @endif
        <li @if ($menu->page->alias == $alias) class="active" @endif><a href="/{{ $link }}">{{ $menu['name'] }}</a></li>
        @endforeach
      </ul>
      @endif
    </nav>
  </div>

  {{-- Основной контент --}}

  @yield('content')

  {{-- Модальные окна --}}
  @yield('modals')

  {{-- Footer --}}
  <footer class="grid-x align-center footer">
    <div class="small-11 medium-10 cell">
      <div class="grid-x align-right align-middle">
        <div class="small-11 medium-11 cell text-center medium-text-right studio"> 
          <span>Разработка сайта: <a href="http://creativebob.ru" target="_blank">Creative<em>Bob</em> Studio</a></span>
        </div>
      </div>
    </div>
  </footer>

  {{-- Скрипты --}}
  <script src="/project/js/vendor/what-input.js"></script>
  <script src="/project/js/vendor/foundation.js"></script>
  <script src="/project/js/app.js"></script>

  <!-- Наши скрипты -->
  {{-- Скрипты --}}
  @yield('scripts')
  <script type="text/javascript">

  </script>
</body>
</html>