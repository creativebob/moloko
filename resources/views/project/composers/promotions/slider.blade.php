@if($promotions->count() > 0)
    <aside class="cell aside-actions small-12">
        <div class="grid-x contur wrap-slick">
            <div class="cell small-12 block-actions single-item">
                @foreach($promotions as $promotion)
                    <div class="item-slide">
                        @if($promotion->mode == 'photo')
                            <a href="{{ $promotion->link }}">
                                <picture>
                                    <source media="(max-width: 420px)" srcset="{{ $promotion->tiny->path }} 420w" sizes="100vw">
                                    <source media="(max-width: 800px)" srcset="{{ $promotion->small->path }} 800w" sizes="100vw">
                                    <source media="(max-width: 1024px)" srcset="{{ $promotion->medium->path }} 1024w" sizes="100vw">
                                    <source media="(max-width: 1280px)" srcset="{{ $promotion->large->path }} 1280w" sizes="100vw">
                                    <source media="(max-width: 2000px)" srcset="{{ $promotion->large_x->path }} 2000w" sizes="200vw">
                                    <img srcset="{{ $promotion->medium->path }} 1024w">
                                </picture>
                            </a>
                        @else
                            {!! $promotion->horizontal !!}
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="line grid-x">
            <div class="cell small-12">
                <div id="hider-promotions">
                         <span class="actions-text">
                         </span>
                </div>
            </div>
        </div>
    </aside>
@endif


@push('scripts')
    <script type="application/javascript">

        let promotion = window.sessionStorage.getItem('promotions') ?  window.sessionStorage.getItem('promotions') : window.sessionStorage.setItem('promotions', 'open');

        if (window.sessionStorage.getItem('promotions') == 'open') {
            $('.wrap-slick').removeClass('hide-slide').removeClass('show-slide');
            $('.wrap-slick').addClass('show-slide');

            $('#hider-promotions').removeClass('hider-on');
        } else {
            $('.wrap-slick').removeClass('hide-slide').removeClass('show-slide');
            $('.wrap-slick').addClass('hide-slide');

            $('#hider-promotions').addClass('hider-on');
        }

        $(document).on('click', '#hider-promotions', function() {
            $('.wrap-slick').toggleClass('hide-slide show-slide');

            if ($('.wrap-slick').hasClass('hide-slide')) {
                window.sessionStorage.setItem('promotions', 'close');
            } else {
                window.sessionStorage.setItem('promotions', 'open');
            }

            $('#hider-promotions').toggleClass('hider-on');

            // Запустить движение слайдов
            // $('.single-item').slick('slickPlay');
        });

        $(".single-item").slick({

            // normal options...
            infinite: false,
            // arrows: false,

            // the magic
            responsive: [{

                breakpoint: 2000,
                settings: {
                    dots: true
                }

            }, {

                breakpoint: 1024,
                settings: {
                    infinite: true,
                    dots: true
                }

            }, {

                breakpoint: 600,
                settings: {
                    dots: true
                }

            }, {

                breakpoint: 300,
                settings: "unslick" // destroys slick

            }]
        });
    </script>
@endpush
