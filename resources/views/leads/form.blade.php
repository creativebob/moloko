
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ЛИД №: <input id="show-case-number" name="show_case_number" readonly class="case_number_field" value="{{ $lead->case_number }}"> </h2>
    </div>
    <div class="top-bar-right wrap_lead_badget">
        {{-- @include('includes.inputs.digit', ['name' => 'badget', 'value' => $lead->badget, 'decimal_place'=>2]) --}}
    </div>
</div>

<div class="grid-x tabs-wrap inputs">

    <!-- Левый блок -->
    <div class="small-12 medium-5 large-7 cell">

        <!-- Персональная информация -->
        <div class="grid-x">
            <div class="small-12 medium-12 large-8 cell margin-left-15">
                <div class="grid-x grid-padding-x">
                    <div class="small-6 medium-6 cell">
                        <label>Телефон
                            {{ Form::text('main_phone', isset($lead->main_phone->phone) ? $lead->main_phone->phone : null, ['class'=>'phone-field', 'maxlength'=>'17', 'autocomplete'=>'off', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'id'=>'phone', $autofocus, $readonly]) }}
                            <span class="form-error">Укажите номер</span>
                        </label>
                    </div>
                    <div class="small-6 medium-6 large-6 cell">
                        <label>Контактное лицо
                            @include('includes.inputs.name', ['name'=>'name', 'value'=>$lead->name, 'required' => true, 'id' => 'lead_user_name'])
                            <input type="hidden" name="lead_id" value="{{$lead->id }}" id="lead_id" data-lead-id="{{$lead->id }}">
                        </label>
                    </div>
                    <div id="port-autofind" class="small-12 cell">
                    </div>

                    <div class="small-6 medium-6 large-6 cell">
                        @include('system.common.includes.city_search', ['item' => $lead, 'required' => true])
                    </div>
                    <div class="small-6 medium-6 cell">
                        <label>Адрес
                            @php
                            $address = null;
                            if (isset($lead->location->address)) {
                                $address = $lead->location->address;
                            }
                            @endphp
                            @include('includes.inputs.address', ['value'=>$address, 'name'=>'address'])
                        </label>
                    </div>
                </div>
            </div>

            <div class="small-12 medium-12 large-4 cell">
                <!-- Пустой блок -->
                <div class="grid-x grid-padding-x">

                    <div class="small-12 cell">
                        <label>Компания
                            @include('includes.inputs.string', ['name'=>'company_name', 'value'=>$lead->company_name])
                        </label>
                    </div>

                    <div class="small-12 cell">
                        <label>E-mail
                            @include('includes.inputs.email', ['value'=>$lead->email, 'name'=>'email'])
                        </label>
                    </div>

                </div>
            </div>
        </div>

        <!-- ЗАКАЗ -->
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 large-12 cell margin-left-15">
                <ul class="tabs-list" data-tabs id="tabs-extra-leads">
                    <li class="tabs-title is-active" id="tab-order"><a href="#content-panel-order" aria-selected="true">Состав заказа</a></li>

                    @can ('index', App\Client::class)
                        <li class="tabs-title" id="tab-client"><a href="#content-panel-client" aria-selected="true">Карточка клиента</a></li>
                    @endcan

                    {{-- <li class="tabs-title" id="tab-address"><a href="#content-panel-address" aria-selected="true">Адреса</a></li> --}}
                    <li class="tabs-title" id="tab-history"><a href="#content-panel-history" aria-selected="true">История</a></li>
                </ul>


                {{-- Контент доп таба --}}
                <div data-tabs-content="tabs-extra-leads">

                    {{-- ЗАКАЗ --}}
                    <div class="tabs-panel is-active" id="content-panel-order">

                        <div class="grid-x grid-margin-x">
                            <estimate-init-component :estimate='@json($lead->estimate)'></estimate-init-component>
                            <div class="small-12 medium-12 large-12 cell">
                                <estimate-component></estimate-component>
                            </div>
                        </div>
                    </div>
                    {{-- КОНЕЦ ЗАКАЗ --}}


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
                                <span>Клиент: <a href="/admin/clients/{{$lead->client->id}}/edit">{{ $lead->client->clientable->name ?? '' }}</a></span><br>

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
                <div class="tabs-panel" id="content-panel-history">
                    <div class="grid-x grid-padding-x">
                        <div id="port-history" class="small-12 cell">
                        </div>
                    </div>
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
                    @isset($catalogs_goods_data)
                        <li class="tabs-title">
                            <a data-tabs-target="tab-catalog-goods" href="#tab-catalog-goods">Товары</a>
                        </li>
                    @endisset
                @endcan

                @can('create', App\Estimate::class)
                    @isset($catalog_services)
                        <li class="tabs-title">
                            <a data-tabs-target="content-panel-catalog-services" href="#content-panel-catalog-services">Услуги</a>
                        </li>
                    @endisset
                @endcan

                {{-- <li class="tabs-title"><a href="#content-panel-documents" aria-selected="true">Документы</a></li> --}}

                {{-- @can ('index', App\Claim::class)
                <li class="tabs-title">
                    <a data-tabs-target="content-panel-claims" href="#content-panel-claims">Рекламации</a>
                </li>
                @endcan --}}

                <li class="tabs-title"><a href="#content-panel-payments" aria-selected="true">Оплата</a></li>


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
                                        <label>Дата доставки:
                                            @include('includes.inputs.date', ['name' => 'delivery_date', 'value' => isset($lead->delivered_at) ? $lead->delivered_at->format('d.m.Y') : null])
                                        </label>
                                    </div>
                                    <div class="small-3 medium-6 large-3 cell">
                                        <label>Время доставки:
                                            @include('includes.inputs.time', ['name' => 'delivery_time', 'placeholder' => true, 'value' => isset($lead->delivered_at) ? $lead->delivered_at->format('H:i') : null])
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
                @can('index', App\CatalogsGoods::class)
                    @isset($catalogs_goods_data)
                    <div class="tabs-panel" id="tab-catalog-goods">
                        <catalog-goods-component :catalogs-goods-data='@json($catalogs_goods_data)'></catalog-goods-component>
                    </div>
                    @endisset
                @endcan
                {{-- КОНЕЦ КАТАЛОГ ТОВАРОВ --}}

                {{-- КАТАЛОГ УСЛУГ --}}
                @can('index', App\CatalogsService::class)
                    @isset($catalog_services)
                    <div class="tabs-panel" id="content-panel-catalog-services">
                        <div class="grid-x grid-padding-x">

                            {{-- ВЫВОД ПУНКТОВ КАТАЛОГА --}}
                            <div class="shrink cell catalog-bar">
                                <div class="grid-x grid-padding-x">

                                    {{-- ПОИСК ПО УСЛУГАМ --}}
                                    <div class="small-12 cell search-in-catalog-panel">
                                        <label class="label-icon">
                                            <input type="text" name="search" placeholder="Поиск" maxlength="25" autocomplete="off">
                                            <div class="sprite-input-left icon-search"></div>
                                            <span class="form-error">Обязательно нужно логиниться!</span>
                                        </label>
                                    </div>

                                    {{-- СПИСОК ПУНКТОВ КАТАЛОГА --}}

                                    <div class="small-12 cell search-in-catalog-panel">

                                        @include('leads.catalogs.catalogs_items', ['catalog' => $catalog_services, 'type' => 'services'])

                                    </div>
                                </div>
                            </div>

                            {{-- ВЫВОД ПРОЦЕССОВ (УСЛУГ) --}}
                            <div class="auto cell">
                                <div class="grid-x grid-padding-x">

                                    {{-- ПАНЕЛЬ УПРАВЛЕНИЯ ОТОБРАЖЕНИЕМ --}}
                                    <div class="small-12 cell view-settings-panel">
                                        <div class="one-icon-16 icon-view-list icon-button active" id="toggler-view-list"></div>
                                        <div class="one-icon-16 icon-view-block icon-button" id="toggler-view-block"></div>
                                        <div class="one-icon-16 icon-view-card icon-button" id="toggler-view-card"></div>
                                        <div class="one-icon-16 icon-view-setting icon-button" id="open-setting-view"></div>
                                    </div>

                                    {{-- ВЫВОД УСЛУГ --}}
                                    <div id="block-prices_services">
                                    @foreach ($catalog_services->items as $item)
                                        <ul class="small-12 cell products-list view-list" id="block-catalog_services_item-{{ $item->id }}">
                                            @foreach($item->prices as $prices_service)
                                                <li>
                                                    <a class="add-to-estimate" data-price_id="{{ $prices_service->id }}" data-serial="{{ $prices_service->service->serial }}" data-type="services">

                                                        <div class="media-object stack-for-small">
                                                            <div class="media-object-section items-product-img" >
                                                                <div class="thumbnail">
                                                                    <img src="{{ getPhotoPath($prices_service->service->process, 'small') }}">
                                                                </div>
                                                            </div>

                                                            <div class="media-object-section cell">

                                                                <div class="grid-x grid-margin-x">
                                                                    <div class="cell auto">
                                                                        <h4>
                                                                            <span class="items-product-name">{{ $prices_service->service->process->name }}</span>
                                                                            @if($prices_service->service->process->manufacturer)
                                                                                <span class="items-product-manufacturer"> ({{ $prices_service->service->process->manufacturer->name ?? '' }})</span>
                                                                            @endif
                                                                        </h4>
                                                                    </div>

                                                                    <div class="cell shrink wrap-product-price">

                                                                        <span class="items-product-price">{{ num_format($prices_service->price, 0) }}</span>
                                                                    </div>
                                                                </div>
                                                                <p class="items-product-description">{{ $prices_service->service->description }}</p>
                                                            </div>
                                                        </div>

                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @endisset
                @endcan
                {{-- КОНЕЦ КАТАЛОГ УСЛУГ --}}


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

                    {{-- ФАКТ ОПЛАТЫ --}}
                    <div class="tabs-panel" id="content-panel-payments">
                        <div class="grid-x grid-padding-x">
                            <div class="cell small-4">
                                <label>Сумма:
                                    @include('includes.inputs.digit', ['name' => 'payment', 'value' => num_format($lead->payment, 2), 'decimal_place'=>2])
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- АТТРИБУЦИЯ --}}
                    <div class="tabs-panel" id="content-panel-attribution">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">
                                <table class="table-attributions">
                                    <tr>
                                        <tr>
                                            <td>Тип обращения: </td>
                                            <td id="lead-type-name">{{ $lead->lead_type->name ?? ''}}</td>
                                            <td>

                                                @if (($lead->manager_id == Auth::user()->id) || (Auth::user()->staff[0]->position_id == 4))
                                                <a id="change-lead-type" class="button tiny">Изменить</a>
                                                @endif

                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Способ обращения: </td>
                                            <td>

                                                {{-- Будем мутить селект в ручную --}}

                                                {{-- @php
                                                if($lead->lead_method->mode != 1){

                                                $disabled_method_list = 'disabled';} else {
                                                $disabled_method_list = '';};
                                                @endphp --}}

                                                @include('includes.selects.lead_methods', ['lead_method_id' => $lead->lead_method_id])

                                            </td><td></td>
                                        </tr>
                                        <td>Интерес: </td>
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
                                                @if ($stocks->isNotEmpty())
                                                <select-stocks-component :stock-id="{{ $lead->estimate->stock_id }}" :stocks='@json($stocks)'></select-stocks-component>
{{--                                                @include('includes.selects.stocks', ['stock_id' => $lead->estimate->stock_id, 'disabled' =>  $disabled])--}}
                                                    @endif
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>Источник: </td><td>{{ $lead->source->name ?? ''}}</td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Сайт: </td><td>{{ $lead->site->name ?? ''}}</td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Тип трафика: </td><td>{{ $lead->medium->name ?? ''}}</td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Рекламная кампания: </td><td>{{ $lead->campaign_id ?? ''}}</td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Объявление: </td><td>{{ $lead->utm_content ?? ''}}</td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Ключевая фраза: </td><td>{{ $lead->utm_term ?? ''}}</td><td></td>
                                        </tr>
                                        <tr>
                                            <td>Менеджер: </td><td>{{ $lead->manager->name }}</td>
                                            <td>
                                                @if (extra_right('lead-appointment') || (extra_right('lead-appointment-self') && ($lead->manager_id == Auth::user()->id)) || ($lead->manager_id == Auth::user()->id))
                                                <a id="lead-free" class="button tiny">Освободить</a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Активных задач: </td><td>{{ $lead->challenges_active_count ?? ''}}</td><td></td>
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


        <!-- Кнопка сохранить -->
        <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">
            @can('update', $lead)
            {{ Form::submit($submitButtonText, ['class'=>'button']) }}
            @else
            {{ Form::submit($submitButtonText, ['class'=>'button', $disabled_leadbot]) }}
            @endcan
        </div>

        <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">
            <estimate-sale-button-component></estimate-sale-button-component>
        </div>
</div>

    {{-- Подключаем ПОИСК обращений и заказов по номеру телефона --}}
    @include('leads.autofind-lead-script')
    @include('includes.scripts.product-to-estimate-script')
