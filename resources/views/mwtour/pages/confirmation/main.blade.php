<!-- noindex -->
<div class="grid-x grid-padding-x">
    <main class="cell small-12">

        {{-- Заголовок --}}
        @include('mwtour.pages.common.title')

        <div class="grid-x grid-padding-x">

            {{-- <div class="cell small-12 medium-3 wrap-img-pages">
                <img src="img/{{ $site->alias }}/pages/santa-claus.png">
            </div> --}}
            <div class="cell small-12 medium-6 page-content">
                {!! $page->content !!}

                @if (session('confirmation'))
                    <p>Отлично, мы получили ваш заказ: №{{ session('confirmation')['lead']->id }} от {{ session('confirmation')['lead']->created_at->format('d.m.Y') }} года<br>
                        Сумма вашего заказа: {{ num_format(session('confirmation')['lead']->badget, 0) }} руб.
                    </p>
                @endif

            </div>
        </div>
    </main>
</div>
<!-- /noindex -->
