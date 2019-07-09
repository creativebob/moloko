@php
$cities = config()->get('cities');
$city = $cities[0];
$alias = 'company';
@endphp

<!doctype html>
<html class="no-js" lang="ru" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <meta name="yandex-verification" content="d3eb935c95921e47" />
  <script src="/js/jquery.latest.min.js"></script>
  <link rel="stylesheet" href="/css/foundation.css">
  <link rel="stylesheet" href="/css/app.css">
  <link  href="/js/plugins/fancybox/dist/jquery.fancybox.min.css" rel="stylesheet">
  <script src="/js/plugins/fancybox/dist/jquery.fancybox.min.js"></script>
  <title>{{ $exception->getMessage() }} | Воротная компания "Марс"</title>
  <meta name="description" content="Ошибка">
</head>
<body>

  <header id="anchor-menu" data-magellan-target="anchor-menu">
    <nav data-sticky-container>
      <div data-sticky data-options="marginTop:0;" style="width:100%;" data-anchor="ms">
        <div class="tb-sticky">
          <div class="grid-x top-bar stacked-for-medium">
            <div class="top-bar-title">
              <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
                <button class="menu-icon light" type="button" data-toggle></button>
              </span>
              <strong></strong>
            </div>
            <div id="responsive-menu">
              <div class="top-bar-right">
                @if(!empty($navigations['main']))
                <ul class="dropdown vertical menu medium-horizontal large-horizontal" data-dropdown-menu>
                  @foreach ($navigations['main']['menus'] as $menu)
                  @if (empty($menu['alias']))
                  @php
                  $link = '/'.$menu['page']['alias'];
                  @endphp
                  @else
                  @php
                  $link = $menu['menu_alias'];
                  @endphp
                  @endif
                  <li><a href="{{ $link }}">{{ $menu['name'] }}</a></li>
                  @endforeach
                </ul>
                @endif
              </div>
              <div class="top-bar-left add-logo">
                <a href="/"></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="grid-x glob-head">
      <div class="upline cell small-12 medium-8 large-8">
        <div class="wrap-logo">
          <div class="img-wrap">
            <a href="../"><img src="/img/logotype.jpg" class="ourlogo" title="На главную!" alt="logo"></a>
          </div>
          <div class="wrap-toggle-city">

            <button class="change-city" type="button" data-toggle="example-dropdown">Город: <span id="mycity" class="mycity">
              @if (isset($filials[$city]))
              {{ $filials[$city]['location']['city']['name'] }}
              @else
              @php
              $data['title'] = '404';
              $data['name'] = 'Page not found';
              return response()
              ->view('errors.404',$data,404);
              @endphp
              @endif

            </span></button>
            <div class="dropdown-pane toggle-city" id="example-dropdown" data-v-offset="3" data-dropdown data-close-on-click="true">
              <ul class="list-city">

                @foreach ($site['departments'] as $filial)
                <li><a href="/{{ $filial['location']['city']['alias'] }}/{{ $alias }}">{{ $filial['location']['city']['name'] }}</a></li>
                @endforeach

              </ul>
            </div>
          </div>

          <p class="logo-text">Производство и монтаж уличных и гаражных ворот</p>
          <p class="under-logo-text"></p>
        </div>
      </div>
      <div class="cell small-12 medium-4 large-4 contact-block">
        <p class="phone"><a href="tel:{{ callPhone($filials[$city]['phone']) }}">{{ decorPhone($filials[$city]['phone']) }}</a></p>
        <p class="address"><a href="/contacts#map" title="Смотреть на карте"><span class="geo"></span>{{ $filials[$city]['location']['city']['name'] .', '.  $filials[$city]['location']['address'] }}</a></p>
      </div>
    </div>
  </header>

  <div class="grid-x">
    <div class="cell small-12 medium-12 large-12">
      @if(!empty($navigations['general']))
      <ul class="menu top-cat-menu vertical menu medium-horizontal large-horizontal">
        @foreach ($navigations['general']['menus'] as $menu)
        @if (empty($menu['alias']))
        @php
        $link = '/'.$menu['page']['alias'];
        @endphp
        @else
        @php
        $link = $menu['alias'];
        @endphp
        @endif
        <li><a href="{{ $link }}">{{ $menu['name'] }}</a></li>
        @endforeach
      </ul>
      @endif
    </div>
  </div>
  <div class="wrap-main grid-x">
    <main class="cell small-12 medium-9 large-9 main-cont">

      {{ $exception->getMessage() }}

    </main>
  </div>


  <footer class="footer">
    <div class="grid-x">
      <div class="cell small-12 medium-6 large-order-2 block-extra-links">
        <div class="grid-x">
          <div class="cell small-12 medium-6">
            <h4>Воротная компания "Марс":</h4>
            @if(!empty($navigations['footer']))
            <ul>
              @foreach ($navigations['footer']['menus'] as $menu)
              @if (empty($menu['alias']))
              @php
              $link = '/'.$menu['page']['alias'];
              @endphp
              @else
              @php
              $link = $menu['alias'];
              @endphp
              @endif
              <li><a href="{{ $link }}">{{ $menu['name'] }}</a></li>
              @endforeach
            </ul>
            @endif
          </div>
          <div class="cell small-12 medium-6">
            <h4>Мы в соцмедиа:</h4>
            @if(!empty($navigations['social']))
            <ul>
              @foreach ($navigations['social']['menus'] as $menu)
              @if (empty($menu['alias']))
              @php
              $link = 'href=/'.$menu['page']['alias'];
              @endphp
              @else
              @php
              $link = 'href='.$menu['alias'].' target=_blank';
              @endphp
              @endif
              <li><a {{ $link }}>{{ $menu['name'] }}</a></li>
              @endforeach
            </ul>
            @endif
          </div>
        </div>
      </div>
      <div class="cell small-12 medium-6 large-order-1" itemscope itemtype="http://schema.org/Organization">
        <div class="wrap-logo">
          <div class="img-wrap">
            <a href="index.php" itemprop="url"><img itemprop="logo" src="/img/logotype.jpg" class="ourlogo" alt="logo"></a>
            <p itemprop="name" class="hide">Воротная компания "Марс"</p>
          </div>
          <p class="logo-text">Производство и монтаж воротных систем</p>
          <p class="under-logo-text"></p>
        </div>

        <div class="addres-footer" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
          <p>
            <span itemprop="addressLocality">{{ $filials[$city]['location']['city']['name'] }}</span>,
            <span itemprop="streetAddress">{{ $filials[$city]['location']['address'] }}<br>
              <span itemprop="email">info@vorotamars.ru</span><br>
              <span itemprop="telephone">{{ decorPhone($filials[$city]['phone']) }}</span>
            </span>
          </p>
        </div>
      </div>
    </div>

    <div class="grid-x">
      <div class="cell small-12 medium-12 large-12 lower-footer">
        <p>&copy; ООО Воротная компания &laquo;«Марс»&raquo; 2004 - @php echo date("Y"); @endphp</p>
      </div>
    </div>

    <!-- Yandex.Metrika counter -->
    <script>
      (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
          try {
            w.yaCounter38734350 = new Ya.Metrika({
              id:38734350,
              clickmap:true,
              trackLinks:true,
              accurateTrackBounce:true,
              webvisor:true,
              trackHash:true,
              ecommerce:"dataLayer"
            });
          } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
          d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
      })(document, window, "yandex_metrika_callbacks");
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/38734350" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->

    <script type="application/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=QdoMe2vdMxv*3ZRc*dICU/Uyl2zuDc*2NXt0rNyHgSo0IDoL2GQU24WH6rkz*7uZR/Pi7bbpp16Tp9KTsOvI8YePyQ4Fbqf7ltdOHpTJnVVDlv*LfyVvrKGYjZ2al2XHaatuBq6IfYpKVwOibAhy8kulto45iHujX8ctMQ6at14-';</script>
  </footer>
</body>
</html>
<script>


  $(document).ready(function() {

    $('#uper').click(function(){
      var el = $(this).attr('href');
      $('body').animate({
        scrollTop: $(el).offset().top}, 1000);
      return false;
    });

  });

</script>
<!--    <script src="js/vendor/jquery.js"></script> -->
<script src="/js/vendor/what-input.js"></script>
<script src="/js/vendor/foundation.js"></script>
<script src="/js/app.js"></script>



