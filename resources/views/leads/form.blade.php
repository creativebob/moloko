<lead-init-component
    :outlet="{{ $outlet }}"
></lead-init-component>

<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ЛИД №:<input id="show-case-number" name="show_case_number" readonly
                                                class="case_number_field" value="{{ $lead->case_number }}"></h2>
    </div>
    <div class="top-bar-right wrap_lead_badget">

        <lead-errors-component></lead-errors-component>
        {{-- @include('includes.inputs.digit', ['name' => 'badget', 'value' => $lead->badget, 'decimal_place'=>2]) --}}
    </div>
</div>

<div class="grid-x tabs-wrap inputs">

    <!-- Левый блок -->
    <div class="small-12 medium-5 large-7 cell">

    {{--       Персональная информация--}}
    @include('leads.personal', ['item' => $lead ?? auth()->user(), 'manual' => true])


    <!-- ЗАКАЗ -->
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 large-12 cell margin-left-15 wrap-tabs-lead">
                <ul class="tabs-list" data-tabs id="tabs-extra-leads">

                    <li class="tabs-title is-active">
                        <a href="#tab-estimate" aria-selected="true">Состав заказа</a>
                    </li>

                    @can ('index', App\Client::class)
                        <li class="tabs-title">
                            <a data-tabs-target="content-panel-client" href="#content-panel-client">Карточка клиента</a>
                        </li>
                    @endcan

                    {{-- <li class="tabs-title" id="tab-address"><a href="#content-panel-address" aria-selected="true">Адреса</a></li> --}}
                    <li class="tabs-title">
                        <a data-tabs-target="tab-history" href="#tab-history">История</a>
                    </li>
                </ul>


                {{-- Контент доп таба --}}
                <div data-tabs-content="tabs-extra-leads">

                    {{-- Смета --}}
                    <div class="tabs-panel is-active" id="tab-estimate">

                        <div class="grid-x grid-padding-x wrap-estimate-title">
                            <div class="small-12 medium-shrink cell estimate-title">
                                <p>Клиентский заказ
                                    @if($lead->estimate->registered_at)
                                        № {{ $lead->estimate->number ?? '' }} от {{ $lead->estimate->registered_at->format('d.m.Y') }} <span class="tiny-text">({{ $lead->estimate->registered_at->getTranslatedShortDayName('dd') }})</span>
                                    @endif
                                </p>
                            </div>
                            <div class="small-12 medium-auto cell estimate-control">
                                <a href="/admin/leads/{{ $lead->id }}/print_sticker_stock" target="_blank">
                                    <span class="button-print-stock-sticker" title="Маркер для склада"></span>
                                </a>
                            </div>
                        </div>

                        @include('leads.tabs.estimate')
                    </div>

                    {{-- КЛИЕНТ --}}
                    <div class="tabs-panel" id="content-panel-client">
                        <div class="grid-x grid-padding-x">


                            {{-- <div class="small-12 medium-12 large-12 cell">
                                <label>Страна
                                    @php
                                    $country_id = null;
                                    if (isset($lead->location->country_id)) {
                                    $country_id = $lead->location->country_id;
                                }
                                @endphp
                                {{ Form::select('country_id', $countries_list, $country_id)}}
                                </label>
                            </div> --}}

                            <div class="small-12 medium-12 cell">
                                <div class="grid-x grid-padding-x">

                                </div>



                                @if($lead->client)
                                    {{--                                    @if ($lead->client->orders_count > 0)--}}
                                    {{--                                        <span>Клиент: <a href="{{ route('clients.edit', $lead->client_id) }}">{{ $lead->client->clientable->name ?? '' }}</a></span><br>--}}
                                    {{--                                    @endif--}}

                                    <span>Лояльность: {{ $lead->client->loyalty->name ?? '' }}</span>
                                @else

                                    {{-- Подключаем клиентов --}}
                                    @include('includes.contragents.fieldset', ['item' => $lead])

                                @endif


                            </div>
                        </div>
                    </div>
                    {{-- КОНЕЦ КЛИЕНТ --}}

                    {{-- АДРЕСА --}}
                    <div class="tabs-panel" id="content-panel-address">
                        <div class="grid-x grid-padding-x">
                            <!-- <div id="port-address" class="small-12 cell">
                            </div>  -->
                        </div>
                    </div>
                    {{-- КОНЕЦ АДРЕСА --}}

                    {{-- ИСТОРИЯ --}}
                    <div class="tabs-panel" id="tab-history">
                        @include('leads.tabs.history')
                    </div>
                    {{-- КОНЕЦ ИСТОРИЯ --}}

                    {{-- Конец контента доп таба --}}
                </div>

            </div>
        </div>
    </div>


    <!-- Правый блок -->
    <div class="small-12 medium-7 large-5 cell">
        {{ Form::model($lead, [
    'route' => ['leads.update', $lead->id],
    'data-abide', 'novalidate',
    'id' => 'form-lead',
    'files' => 'true'
]) }}
        {{ method_field('PATCH') }}
        {!! Form::hidden('previous_url', url()->previous()) !!}
        <div class="grid-x tabs-right">
            <div class="cell small-12">


                    {{-- <li class="tabs-title"><a href="#content-panel-documents" aria-selected="true">Документы</a></li> --}}

                    {{-- @can ('index', App\Claim::class)
                    <li class="tabs-title">
                        <a data-tabs-target="content-panel-claims" href="#content-panel-claims">Рекламации</a>
                    </li>
                    @endcan --}}
                    <lead-tabs-component></lead-tabs-component>

                    {{-- <li class="tabs-title"><a href="#content-panel-measurements" aria-selected="true">Замеры</a></li> --}}

            </div>

            <div class="small-12 cell">

                <div class="tabs-content tabs-leads" data-tabs-content="tabs-leads">
                    {{-- Взаимодействия: задачи и события --}}
                    <div class="tabs-panel is-active" id="tab-events">
                        @include('leads.tabs.events')
                    </div>

                    {{-- КАТАЛОГ ТОВАРОВ --}}
                    <div class="tabs-panel" id="tab-catalog_goods">
                        @include('leads.tabs.catalogs_goods', ['catalogsIds' => optional($outlet->catalogs_goods)->pluck('id')])
                    </div>

                    {{-- КАТАЛОГ УСЛУГ --}}
                    <div class="tabs-panel" id="tab-catalog_services">
                        @include('leads.tabs.catalogs_services')
                    </div>

                    {{-- ДОКУМЕНТЫ
                    <div class="tabs-panel" id="content-panel-documents">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 large-6 cell">
                            </div>
                        </div>
                    </div> --}}

                    {{-- РЕКЛАМАЦИИ --}}
                    <div class="tabs-panel" id="content-panel-claims">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">

                                @can ('index', App\Claim::class)
                                    <fieldset class="fieldset-challenge">
                                        <legend>Рекламации:</legend>
                                        <div class="grid-x grid-padding-x">
                                            <table class="table-challenges" id="table-challenges">
                                                <thead>
                                                <tr>
                                                    <th>Дата</th>
                                                    <th>Номер</th>
                                                    <th>Обращение</th>
                                                    <th>Описание проблемы</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody id="claims-list">

                                                @if (count($lead->claims) > 0)
                                                    @include('leads.claim', ['claims' => $lead->claims])
                                                @endif

                                                </tbody>
                                            </table>
                                        </div>
                                        @can ('create', App\Claim::class)
                                            <div class="grid-x grid-padding-x align-left">
                                                <div class="small-4 cell">
                                                    @can('update', $lead)
                                                        <a class="button green-button claim-add" data-open="add-claim">Добавить</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endcan
                                    </fieldset>
                                @endcan


                            </div>
                        </div>
                    </div>
                    {{-- КОНЕЦ РЕКЛАМАЦИИ --}}

                    {{-- ЗАМЕРЫ
                    <div class="tabs-panel" id="content-panel-measurements">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 large-6 cell">
                            </div>
                        </div>
                    </div> --}}

                    {{-- ОПЛАТА --}}
                    <div class="tabs-panel" id="tab-payments">
                        @include('leads.tabs.payments')
                    </div>

                    {{-- Аттрибуция --}}
                    <div class="tabs-panel" id="tab-attribution">
                        @include('leads.tabs.attribution')
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
    {{-- КОНЕЦ ПРАВОГО БЛОКА --}}


    {{--    <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">--}}
    {{--        @can('update', $lead)--}}
    {{--            {{ Form::submit($submitButtonText, ['class'=>'button']) }}--}}
    {{--        @else--}}
    {{--            {{ Form::submit($submitButtonText, ['class'=>'button', $disabled_leadbot]) }}--}}
    {{--        @endcan--}}
    {{--    </div>--}}
</div>

{{-- Подключаем ПОИСК обращений и заказов по номеру телефона --}}
@include('leads.autofind-lead-script')
@include('includes.scripts.product-to-estimate-script')
