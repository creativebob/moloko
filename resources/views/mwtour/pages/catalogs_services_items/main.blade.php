<div class="grid-x grid-padding-x">

    <aside class="cell small-12 medium-5 large-3 sidebar" data-sticky-container>
        <div class="sticky" data-sticky data-sticky-on="medium" data-top-anchor="278" data-btm-anchor="wrap-sidebar:bottom" data-margin-top="2">
            @include('project.composers.catalogs_services.sidebar')
            {{-- @include('project.composers.news.images') --}}
        </div>
    </aside>

    <main class="cell small-12 medium-7 large-9 main-content">

        {{-- <span class="desc-green">{{ $catalogs_services_item->category->name }}:</span> --}}
        <h1>{{ $catalogs_services_item->header ?? $catalogs_services_item->name}} @if(request('part-brand'))- {{ request('part-brand') }}@endif @if(request('car-brand'))- {{ request('car-brand') }}@endif</h1>

        @if(!empty($catalogs_services_item->description))
            <article>
                {!! $catalogs_services_item->description !!}
            </article>
        @endif

        @include('project.composers.prices_services.impacts')

        @if($catalogs_services_item->prices->isNotEmpty())
            <section class="grid-x grid-padding-x">
                <div class="cell small-12">
                    <h2 class="yellow-header">Перечень услуг @if(request('part-brand')) по бренду - <strong>{{ request('part-brand') }}</strong>@endif @if(request('car-brand')) по бренду - <strong>{{ request('car-brand') }}</strong>@endif</h2>
                    <div class="grid-x grid-padding-x">
                        <div class="cell small-12 medium-12 large-9">
                            <table class="unstriped" id="table-prices_services">
                                {{--                         <thead>
                                                            <tr>
                                                                <th>Название</th>
                                                                <th>Время выполнения</th>
                                                                <th>Стоимость</th>
                                                            </tr>
                                                        </thead> --}}

                                <tbody>

                                @include('viandiesel.pages.catalogs_services_items.prices_services', ['pricesServices' => $catalogs_services_item->prices()
                                ->filter()
                                ->oldest('sort')
                                ->get()])

                                </tbody>
                            </table>
                            <br>
                            <p class="catalog-desc">Перечень опубликованных услуг и их стоимость может отличаться от фактических. Список
                                приведен для ознакомления с порядком цен. Не является публичной офертой.</p>
                        </div>
                        <div class="cell small-12 medium-12 large-3 experts">
                            <h3 class="h3">Специалисты по этому направлению:</h3>
                            @include('project.composers.prices_services.providers', ['prices_services' => $catalogs_services_item->prices])
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section class="grid-x grid-padding-x align-center">
            <div class="cell small-12 large-4">
                @php
                    $title = "{$catalogs_services_item->category->name}: ";
                    if($catalogs_services_item->level == 3) {
                        $title .= "{$catalogs_services_item->parent->name} - ";
                    }
                    $title .= "{$catalogs_services_item->name}";
                @endphp
                @include('viandiesel.pages.common.forms.call', ['msg' => "Клиент просит перезвонить!\r\n Сообщение отправлено со страницы: {$title}"])
            </div>
        </section>

    </main>
</div>

@push('scripts')
    <script>
        // Подсвечиваем меню сайдбара
        let active = $('.accordion-menu .active');
        if (active) {
            if (active.data('level') > 2) {
                let parent = active.closest('.nested').addClass('is-active');
                Foundation.reInit($('.accordion-menu'));
                // $('#sidebar-catalog_services').foundation('down', parent);
            }
        }
    </script>
@endpush
