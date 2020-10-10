{{--<lead-init-component--}}
{{--    :lead="{{ $lead }}"--}}
{{--></lead-init-component>--}}

<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ЛИД №:<input id="show-case-number" name="show_case_number" readonly
                                                class="case_number_field" value="{{ $lead->case_number }}"></h2>
    </div>
    <div class="top-bar-right wrap_lead_badget">
        {{-- @include('includes.inputs.digit', ['name' => 'badget', 'value' => $lead->badget, 'decimal_place'=>2]) --}}
    </div>
</div>

<div class="grid-x tabs-wrap inputs">

    <!-- Левый блок -->
    <div class="small-12 medium-5 large-7 cell">

    {{--       Персональная информация--}}
    @include('leads.personal', ['item' => $lead ?? auth()->user()])


    <!-- ЗАКАЗ -->
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 large-12 cell margin-left-15 wrap-tabs-lead">
                <ul class="tabs-list" data-tabs id="tabs-extra-leads">

                    <li class="tabs-title is-active">
                        <a href="#tab-estimate" aria-selected="true">Состав заказа</a>
                    </li>

                    @can ('index', App\Client::class)
                        <li class="tabs-title" id="tab-client"><a href="#content-panel-client" aria-selected="true">Карточка
                                клиента</a></li>
                    @endcan

                    {{-- <li class="tabs-title" id="tab-address"><a href="#content-panel-address" aria-selected="true">Адреса</a></li> --}}
                    <li class="tabs-title" id="tab-history"><a href="#tab-history" aria-selected="true">История</a>
                    </li>
                </ul>


                {{-- Контент доп таба --}}
                <div data-tabs-content="tabs-extra-leads">

                    {{-- Смета --}}
                    <div class="tabs-panel is-active" id="tab-estimate">
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
        <div class="grid-x tabs-right">
            <div class="small-12 cell">
                <ul class="tabs-list" data-tabs id="tabs-leads">
                    <li class="tabs-title is-active">
                        <a href="#content-panel-notes" aria-selected="true">События</a>
                    </li>

                    @can('create', App\Estimate::class)
                        <li class="tabs-title">
                            <a data-tabs-target="tab-catalog_goods" href="#tab-catalog_goods">Товары</a>
                        </li>
                    @endcan

                    @can('create', App\Estimate::class)
                        <li class="tabs-title">
                            <a data-tabs-target="tab-catalog_services" href="#tab-catalog_services">Услуги</a>
                        </li>
                    @endcan

                    {{-- <li class="tabs-title"><a href="#content-panel-documents" aria-selected="true">Документы</a></li> --}}

                    {{-- @can ('index', App\Claim::class)
                    <li class="tabs-title">
                        <a data-tabs-target="content-panel-claims" href="#content-panel-claims">Рекламации</a>
                    </li>
                    @endcan --}}
                    <tab-payments-component></tab-payments-component>

                    {{-- <li class="tabs-title"><a href="#content-panel-measurements" aria-selected="true">Замеры</a></li> --}}
                    <li class="tabs-title" id="tab-attribution">
                        <a data-tabs-target="content-panel-attribution" href="#content-panel-attribution">Аттрибуция</a>
                    </li>
                </ul>
            </div>

            <div class="small-12 cell">

                <div class="tabs-content tabs-leads" data-tabs-content="tabs-leads">
                    {{-- Взаимодействия: задачи и события --}}
                    <div class="tabs-panel is-active" id="content-panel-notes">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 large-12 cell">


                                <fieldset class="fieldset-challenge">
                                    <legend>Контроль процесса:</legend>
                                    <div class="grid-x grid-padding-x">
                                        <div class="small-12 large-6 cell">

                                            {{-- Подключаем этапы процесса --}}
                                            @include('includes.selects.stages', ['value' => $lead->stage_id])

                                        </div>
                                        <div class="small-3 medium-6 large-3 cell">
                                            <label>Дата отгрузки
                                                <pickmeup-component
                                                    name="shipment_date"
                                                    value="{{ isset($lead->shipment_at) ? $lead->shipment_at : null }}"
                                                ></pickmeup-component>
                                            </label>
                                            {{--                                        @include('includes.inputs.date', ['name' => 'shipment_date', 'value' => isset($lead->shipment_at) ? $lead->shipment_at->format('d.m.Y') : null])--}}
                                        </div>
                                        <div class="small-3 medium-6 large-3 cell">
                                            <label>Время отгрузки:
                                                @include('includes.inputs.time', ['name' => 'shipment_time', 'placeholder' => true, 'value' => isset($lead->shipment_at) ? $lead->shipment_at->format('H:i') : null])
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>

                                {{-- Подключаем задачи --}}
                                @include('includes.challenges.fieldset', ['item' => $lead])

                                {{-- Подключаем комментарии --}}
                                @include('includes.notes.fieldset', ['item' => $lead])
                            </div>
                        </div>
                    </div>

                    {{-- КАТАЛОГ ТОВАРОВ --}}
                    <div class="tabs-panel" id="tab-catalog_goods">
                        @include('leads.tabs.catalogs_goods')
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

                    {{-- АТТРИБУЦИЯ --}}
                    <div class="tabs-panel" id="content-panel-attribution">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">
                                <table class="table-attributions">
                                    <tr>
                                    <tr>
                                        <td>Тип обращения:</td>
                                        <td id="lead-type-name">{{ $lead->lead_type->name ?? ''}}</td>
                                        <td>

                                            @if (($lead->manager_id == Auth::user()->id) || (Auth::user()->staff[0]->position_id == 4))
                                                <a id="change-lead-type" class="button tiny">Изменить</a>
                                            @endif

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Способ обращения:</td>
                                        <td>

                                            {{-- Будем мутить селект в ручную --}}

                                            {{-- @php
                                            if($lead->lead_method->mode != 1){

                                            $disabled_method_list = 'disabled';} else {
                                            $disabled_method_list = '';};
                                            @endphp --}}

                                            @include('includes.selects.lead_methods', ['lead_method_id' => $lead->lead_method_id])

                                        </td>
                                        <td></td>
                                    </tr>
                                    <td>Интерес:</td>
                                    <td>
                                        {{ Form::select('choice_tag', $choices, genChoiceTag($lead), ['disabled' => ($lead->lead_method_id == 2)]) }}</td>
                                    <td></td>
                                    </tr>
                                    <tr>
                                        <td>Предварительная стоимость:</td>
                                        <td>
                                            <lead-badget-component></lead-badget-component>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Склад списания:</td>
                                        <td>
                                            @php
                                                $disabled = null;
                                                    if ($lead->estimate->is_saled == 1 || $lead->estimate->is_reserved == 1) {
                                                    $disabled = true;
                                                }
                                            @endphp
                                            {{--                                                @if ($stocks->isNotEmpty())--}}
                                            {{--                                                <select-stocks-component :stock-id="{{ $lead->estimate->stock_id }}" :stocks='@json($stocks)'></select-stocks-component>--}}
                                            {{--                                                @include('includes.selects.stocks', ['stock_id' => $lead->estimate->stock_id, 'disabled' =>  $disabled])--}}
                                            {{--                                                    @endif--}}
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Источник:</td>
                                        <td>{{ $lead->source->name ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Сайт:</td>
                                        <td>{{ $lead->site->name ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Тип трафика:</td>
                                        <td>{{ $lead->medium->name ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Рекламная кампания:</td>
                                        <td>{{ $lead->campaign_id ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Объявление:</td>
                                        <td>{{ $lead->utm_content ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Ключевая фраза:</td>
                                        <td>{{ $lead->utm_term ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Менеджер:</td>
                                        <td>{{ $lead->manager->name }}</td>
                                        <td>
                                            @if (extra_right('lead-appointment') || (extra_right('lead-appointment-self') && ($lead->manager_id == Auth::user()->id)) || ($lead->manager_id == Auth::user()->id))
                                                <a id="lead-free" class="button tiny">Освободить</a>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Активных задач:</td>
                                        <td>{{ $lead->challenges_active_count ?? ''}}</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- КОНЕЦ АТТРИБУЦИИ --}}


                </div>

            </div>

        </div>

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
