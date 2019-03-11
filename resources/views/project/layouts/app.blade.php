<!doctype html>
<html class="no-js" lang="ru" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon">
    <meta name="yandex-verification" content="d3eb935c95921e47" />
    <script src="{{ asset('/project/js/jquery.latest.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('/project/css/foundation.css') }}">
    <link rel="stylesheet" href="{{ asset('/project/css/app.css') }}">

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link  href="{{ asset('/project/js/plugins/fancybox/dist/jquery.fancybox.min.css') }}" rel="stylesheet">
    <script src="{{ asset('/project/js/plugins/fancybox/dist/jquery.fancybox.min.js') }}"></script>
    <script type="text/javascript">

</script>

@yield('inhead')

@yield('title')
</head>
<body>

    <header id="anchor-menu">
        <nav data-sticky-container>
            <div data-sticky data-options="marginTop:0;" style="width:100%;" data-anchor="ms">
                <span data-responsive-toggle="responsive-menu" data-hide-for="medium">
                    <button class="menu-icon light" type="button" data-toggle></button>
                </span>
                <div class="top-bar stacked-for-medium">

                    <div id="responsive-menu" style="width:100%;">
                        <div class="grid-x">

                            @isset ($navigations['top'][0])

                            <ul class="small-12 cell dropdown vertical menu medium-horizontal align-right" data-dropdown-menu>

                                @foreach ($navigations['top'][0]->menus as $menu)



                                <li @isset ($page) @if ($menu->page->alias == $page->alias) class="active" @endif @endisset>
                                    <a href="/{{ isset($menu->alias) ? $menu->alias : $menu->page->alias }}">{{ $menu->name }}</a>
                                </li>

                                @endforeach
                            </ul>

                            @endisset

                        </div>
                    </div>
                </div>

            </div>
        </nav>

        <div class="grid-x grid-padding-x glob-head">
            <div class="upline cell small-12 medium-8 large-8">
                <div class="wrap-logo">
                    <div class="img-wrap">
                        <a href="../">
                            {{-- <img src="{{ asset('/project/img/logotype.jpg') }}" class="ourlogo" title="На главную!" alt="logo"> --}}
                        </a>
                    </div>
                    <div class="wrap-toggle-city">

                        {{-- @include('project.includes.partials.cities_list') --}}

                    </div>

                    <p class="logo-text">Текст</p>
                    <p class="under-logo-text"></p>
                </div>
            </div>
            <div class="cell small-11 medium-4 large-4 contact-block">

                @include('project.includes.partials.filials_with_link_to_map')

            </div>
        </div>
    </header>

    @isset ($navigations['top'])

    <div class="grid-x grid-padding-x">
        <div class="cell small-12 medium-12 large-12">

            @foreach ($navigations['top']->take(1) as $navigation)

            <ul class="menu top-cat-menu vertical menu medium-horizontal large-horizontal">

                @foreach ($navigation->menus as $menu)

                <li @isset ($page) {{ ($menu->page->alias == $page->alias) ? 'class="active"' : '' }} @endisset>
                    <a href="/{{ isset($menu->alias) ? $menu->alias : $menu->page->alias }}">{{ $menu->name }}</a>
                </li>

                @endforeach

            </ul>

            @endforeach

        </div>
    </div>

    @endisset

    @yield('content')


    <footer class="footer">
        <div class="grid-x">
            <div class="cell small-12 medium-6 large-order-2 block-extra-links">

                @isset ($navigations['bottom'])
                <div class="grid-x">

                    @foreach ($navigations['bottom']->take(2) as $navigation)

                    <div class="cell small-12 medium-6">
                        <h4>{{ $navigation->name }}:</h4>
                        <ul>

                            @foreach ($navigation->menus as $menu)
                            <li>

                                @if ($menu->new_blank == 1)

                                <a href="{{ $menu->alias }}" target="_blank">{{ $menu->name }}</a>

                                @else

                                <a href="/{{ isset($menu->alias) ? $menu->alias : $menu->page->alias }}">{{ $menu->name }}</a>

                                @endif

                            </li>
                            @endforeach

                        </ul>
                    </div>

                    @endforeach

                </div>
                @endisset
            </div>
            <div class="cell small-12 medium-6 large-order-1" itemscope itemtype="http://schema.org/Organization">
                <div class="wrap-logo">
                    <div class="img-wrap">
                        <a href="company" itemprop="url">
                            {{-- <img itemprop="logo" src="{{ asset('/project/img/logotype.jpg') }}" class="ourlogo" alt="logo"> --}}
                        </a>
                        <p itemprop="name" class="hide">{{ $site->company->name }}</p>
                    </div>
                    <p class="logo-text">Текст</p>
                    <p class="under-logo-text"></p>
                </div>

                <div class="addres-footer" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <p>
                        @include('project.includes.partials.filials_info')
                    </p>
                </div>
            </div>
        </div>

        <div class="grid-x">
            <div class="cell small-12 medium-12 large-12 lower-footer">
                <p>&copy; {{ $site->company->name }} 2004 - @php echo date("Y"); @endphp</p>
            </div>
        </div>

    </footer>

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
    <script src="{{ asset('/project/js/vendor/what-input.js') }}"></script>
    <script src="{{ asset('/project/js/vendor/foundation.js') }}"></script>
    <script src="{{ asset('/project/js/app.js') }}"></script>
    <script src="{{ asset('/project/js/jquery.inputmask.min.js') }}"></script>
    <script>
        $('.phone-field').mask('8(000) 000-00-00');

    // Prevent small screen page refresh sticky bug
    $(window).on('sticky.zf.unstuckfrom:bottom', function(e) {
        if (!Foundation.MediaQuery.atLeast('medium')) {

            $(e.target).removeClass('is-anchored is-at-bottom').attr('style', '');
        }
    });

    // $(".chapters").click(function () {
    //   $(".left-sidebar").toggleClass("sw");
    // });

    $(window).load(function(){
        $(window).trigger('resize');
    });


</script>
<script>
    control_slide = 0;
    lenslide = $(".myslider li").size();

    $(document).ready(function() {
        $(".myslider").each(function () { // обрабатываем каждый слайдер
            var obj = $(this);
            $(obj).append("<div class='nav'></div>");
            $(obj).find("li").each(function () {
                $(obj).find(".nav").append("<span rel='"+$(this).index()+"'></span>"); // добавляем блок навигации
                $(this).addClass("myslider"+$(this).index());
            });
            $(obj).find("span").first().addClass("on"); // делаем активным первый элемент меню
        });
    });

    function sliderJS (obj, sl) { // slider function
        var ul = $(sl).find("ul"); // находим блок
        var bl = $(sl).find("li.myslider"+obj); // находим любой из элементов блока
        var step = $(bl).width(); // ширина объекта
        $(ul).animate({marginLeft: "-"+step*obj}, 500); // 500 это скорость перемотки
    };

    $(document).on("click", ".myslider .nav span", function() { // slider click navigate
        var sl = $(this).closest(".myslider"); // находим, в каком блоке был клик
        $(sl).find("span").removeClass("on"); // убираем активный элемент
        $(this).addClass("on"); // делаем активным текущий
        var obj = $(this).attr("rel"); // узнаем его номер
        // obj = obj + 1;
        sliderJS(obj, sl); // слайдим
        return false;
    });

    $(document).on("click", ".sliderbut-l", function() { // slider click navigate
        var sl = $(".myslider");

        if(control_slide > 0){
          var el = $(".on");
          el.removeClass("on");
          control_slide = control_slide - 1;
          var elem = $('[rel = ' + control_slide + ']');
          elem.addClass("on");
            sliderJS(control_slide, sl); // слайдим
        };
        return false;
    });

    $(document).on("click", ".sliderbut-r", function() { // slider click navigate
        var sl = $(".myslider");

        if(control_slide < lenslide-1){
          var el = $(".on");
          el.removeClass("on");
          control_slide = control_slide*1 + 1;
          var elem = $('[rel = ' + control_slide + ']');
          elem.addClass("on");
          sliderJS(control_slide, sl); // слайдим
      };
      return false;
  });
</script>
<!-- <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = location.protocol + '//vk.com/rtrg?r=QdoMe2vdMxv*3ZRc*dICU/Uyl2zuDc*2NXt0rNyHgSo0IDoL2GQU24WH6rkz*7uZR/Pi7bbpp16Tp9KTsOvI8YePyQ4Fbqf7ltdOHpTJnVVDlv*LfyVvrKGYjZ2al2XHaatuBq6IfYpKVwOibAhy8kulto45iHujX8ctMQ6at14-';</script> -->

<!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
    // (function(){ var widget_id = '7hcH3f8czq';var d=document;var w=window;function l(){var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();
</script>
<!-- {/literal} END JIVOSITE CODE -->

@yield('scripts')
</body>
</html>



