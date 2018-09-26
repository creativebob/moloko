

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-5 large-6 cell">


        @if ($errors->any())

        <div class="alert callout" data-closable>
            <h5>Неправильный формат данных:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        @endif

        <!-- Персональная информация -->
        <div class="grid-x">
            <div class="small-12 medium-12 large-8 cell">

                <div class="grid-x grid-padding-x">
                    <div class="small-6 medium-6 cell">
                        <label>Телефон
                            {{ Form::text('main_phone', isset($lead->main_phone->phone) ? $lead->main_phone->phone : null, ['class'=>'phone-field', 'maxlength'=>'17', 'autocomplete'=>'off', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'id'=>'phone', $autofocus, $readonly]) }}
                            <span class="form-error">Укажите номер</span>

                        </label>

                    </div>
                    <div class="small-6 medium-6 large-6 cell">
                        <label>Контактное лицо
                            @include('includes.inputs.name', ['name'=>'name', 'value'=>$lead->name, 'required'=>'required'])
                            <input type="hidden" id="lead_id" data-lead-id="{{$lead->id }}">
                        </label>
                    </div>
                    <div id="port-autofind" class="small-12 cell">
                    </div>
                    <div class="small-6 medium-6 large-6 cell">
                        <label class="input-icon">Введите город
                            @php
                            $city_name = null;
                            $city_id = null;
                            if(isset($lead->location->city->name)) {
                            $city_name = $lead->location->city->name;
                            $city_id = $lead->location->city->id;
                        }
                        @endphp
                        @include('includes.inputs.city_search', ['city_value'=>$city_name, 'city_id_value'=>$city_id, 'required'=>'required'])
                    </label>
                </div>
                <div class="small-6 medium-6 cell">
                    <label>Адрес
                        @php
                        $address = null;
                        if (isset($lead->location->address)) {
                        $address = $lead->location->address;
                    }
                    @endphp
                    @include('includes.inputs.address', ['value'=>$address, 'name'=>'address', 'required'=>''])
                </label>
            </div>
        </div>

        <!-- Пустой блок -->
        <div class="grid-x grid-padding-x">
            <div class="small-6 medium-6 large-6 cell">
                <label>Бюджет
                    @include('includes.inputs.digit', ['name'=>'badget', 'value'=>$lead->badget, 'required'=>''])
                </label>
            </div>
            <div class="small-6 medium-6 large-6 cell">
                <label>Этап
                    {{ Form::select('stage_id', $stages_list, $lead->stage_id) }}
                </label>
            </div>
<!--             <div class="small-6 medium-6 large-6 cell">
                <label>Менеджер
                    @include('includes.inputs.string', ['name'=>'manager', 'value'=>$lead->manager->name, 'required'=>''])
                </label>
            </div> -->
        </div>

    </div>

    <div class="small-12 medium-12 large-4 cell">
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

        {{-- <div class="small-12 cell">
            <label>Компания
                <div class="switch tiny">
                  <input class="switch-input" id="tinySwitch" type="checkbox" name="exampleSwitch">
                  <label class="switch-paddle" for="tinySwitch">
                    <span class="show-for-sr">Tiny Sandwiches Enabled</span>
                  </label>
                </div>
            </label> 
        </div> --}}
        <div class="small-12 medium-12 cell">
            <label>Почта
                @include('includes.inputs.email', ['value'=>$lead->email, 'name'=>'email', 'required'=>''])
            </label> 
        </div>
        <div class="small-12 cell">
            <label>Компания
                @include('includes.inputs.string', ['name'=>'company_name', 'value'=>$lead->company_name, 'required'=>''])
            </label>
        </div>
    </div>
</div>

</div>

<!-- ЗАКАЗ -->
<div class="grid-x grid-padding-x">
    <div class="small-12 medium-12 large-12 cell">
        <ul class="tabs-list" data-tabs id="tabs-extra-leads">
            <li class="tabs-title is-active" id="tab-order"><a href="#content-panel-order" aria-selected="true">Заказ</a></li>
            {{-- <li class="tabs-title" id="tab-client"><a href="#content-panel-client" aria-selected="true">Клиент</a></li> --}}
            <li class="tabs-title" id="tab-history"><a href="#content-panel-history" aria-selected="true">История</a></li>
        </ul>



        {{-- Контент доп таба --}}
        <div data-tabs-content="tabs-extra-leads">

            {{-- ЗАКАЗ --}}
            <div class="tabs-panel is-active" id="content-panel-order">

                <div class="grid-x">
                    <div class="small-12 medium-12 large-12 cell">

                        {{--
                            <table class="table-order" id="table-order">
                                <thead>
                                    <tr>
                                        <th>Наименование</th>
                                        <th>Кол-во</th>
                                        <th>Закуп</th>
                                        <th>ДопРасх</th>
                                        <th>Наценка</th>
                                        <th>Цена</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Откатные ворота ЭКО</td>
                                        <td>1</td>
                                        <td>35 000</td>
                                        <td>1 200</td>
                                        <td>15 000</td>
                                        <td>50 000</td>
                                    </tr>
                                    <tr>
                                        <td>Привод для ворот</td>
                                        <td>1</td>
                                        <td>7 8000</td>
                                        <td>0</td>
                                        <td>5 000</td>
                                        <td>13 800</td>
                                    </tr>
                                </tbody>
                            </table>
                            --}}
                        </div>
                    </div>

                </div>

                {{-- КЛИЕНТ --}}
                {{-- <div class="tabs-panel" id="content-panel-client">
                    <div class="grid-x grid-padding-x">
                        @if (($lead->manager_id == Auth::user()->id) || (Auth::user()->staff[0]->position_id == 4))
                        <a id="lead-free" class="button">Освободить лида</a>
                        @endif
                    </div>
                </div> --}}

                {{-- ИСТОРИЯ --}}
                <div class="tabs-panel" id="content-panel-history">
                    <div class="grid-x grid-padding-x">
                        <div id="port-history" class="small-12 cell">
                        </div>  
                    </div>
                </div>


                {{-- Конец контента доп таба --}}
            </div>

        </div>
    </div>

</div>

<!-- Правый блок -->
<div class="small-12 medium-7 large-6 cell">
    <div class="grid-x tabs-right">
        <div class="small-12 cell">
            <ul class="tabs-list" data-tabs id="tabs-leads">
                <li class="tabs-title is-active"><a href="#content-panel-notes" aria-selected="true">События</a></li>
                <li class="tabs-title"><a href="#content-panel-catalog" aria-selected="true">Каталог</a></li>
                {{-- <li class="tabs-title"><a href="#content-panel-documents" aria-selected="true">Документы</a></li> --}}
                <li class="tabs-title"><a href="#content-panel-claims" aria-selected="true">Рекламации</a></li>
                {{-- <li class="tabs-title"><a href="#content-panel-measurements" aria-selected="true">Замеры</a></li> --}}
                <li class="tabs-title" id="tab-attribution"><a href="#content-panel-attribution" aria-selected="true">Аттрибуция</a></li>
            </ul>
        </div>
    </div>

    <div class="tabs-content tabs-leads" data-tabs-content="tabs-leads">
        {{-- Взаимодействия: задачи и события --}}
        <div class="tabs-panel is-active" id="content-panel-notes">
            <div class="grid-x grid-padding-x">
                <div class="small-12 large-12 cell">
                    {{-- Подключаем задачи --}}
                    @include('includes.challenges.fieldset', ['item' => $lead])

                    {{-- Подключаем комментарии --}}
                    @include('includes.notes.fieldset', ['item' => $lead])
                </div>
            </div>
        </div>

        {{-- Каталог продукции --}}
        <div class="tabs-panel" id="content-panel-catalog">
            <div class="grid-x grid-padding-x">
                <div class="small-12 large-6 cell">
                </div>
            </div>
        </div>

        {{-- Документы 
            <div class="tabs-panel" id="content-panel-documents">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 large-6 cell">
                    </div>
                </div>
            </div> --}}

            {{-- Рекламации --}}
            <div class="tabs-panel" id="content-panel-claims">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 cell">

                        {{-- @can ('index', App\Claim::class) --}}
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
                            {{-- @can ('create', App\Claim::class) --}}
                            <div class="grid-x grid-padding-x align-left">
                                <div class="small-4 cell">
                                    <a class="button green-button claim-add" data-open="add-claim">Добавить</a>
                                </div>
                            </div>
                            {{-- @endcan --}}
                        </fieldset>
                        {{-- @endcan --}}


                    </div>
                </div>
            </div>

            {{-- Замеры 
                <div class="tabs-panel" id="content-panel-measurements">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 large-6 cell">
                        </div>
                    </div>
                </div> --}}

            {{-- АТТРИБУЦИЯ --}}
            <div class="tabs-panel" id="content-panel-attribution">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 cell">
                        <table class="table-attributions">
                            <tr>
                                <tr>
                                    <td>Тип обращения: </td><td>{{ $lead->lead_type->name or ''}}</td><td></td>
                                </tr>
                                <td>Интерес: </td>
                                <td>
                                    @if(!empty($lead->choices_goods_categories->implode('name', ',')))
                                    {{ $lead->choices_goods_categories->implode('name', ',') }}<br>
                                    @endif

                                    @if(!empty($lead->choices_services_categories->implode('name', ',')))
                                    {{ $lead->choices_services_categories->implode('name', ',') }}<br>
                                    @endif

                                    @if(!empty($lead->choices_raws_categories->implode('name', ',')))
                                    {{ $lead->choices_raws_categories->implode('name', ',') }}<br>
                                    @endif
                                </td><td></td>     
                            </tr>
                            <tr>
                                <td>Источник: </td><td>{{ $lead->source->name or ''}}</td><td></td>
                            </tr>
                            <tr>
                                <td>Сайт: </td><td>{{ $lead->site->name or ''}}</td><td></td>
                            </tr>
                            <tr>
                                <td>Тип трафика: </td><td>{{ $lead->medium->name or ''}}</td><td></td>
                            </tr>
                            <tr>
                                <td>Рекламная кампания: </td><td>{{ $lead->campaign->name or ''}}</td><td></td>
                            </tr>
                            <tr>
                                <td>Объявление: </td><td>{{ $lead->utm_content->name or ''}}</td><td></td>
                            </tr>
                            <tr>
                                <td>Ключевая фраза: </td><td>{{ $lead->utm_term->name or ''}}</td><td></td>
                            </tr>
                            <tr>
                                <td>Менеджер: </td><td>{{ $lead->manager->name }}</td>
                                <td>
                                    @if (($lead->manager_id == Auth::user()->id) || (Auth::user()->staff[0]->position_id == 4))
                                        <a id="lead-free" class="button tiny">Освободить</a>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>



            </div>




        </div>

        {{-- Чекбоксы управления 
            @include('includes.control.checkboxes', ['item' => $lead])  
            --}}

            <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
                {{ Form::submit($submitButtonText, ['class'=>'button']) }}
            </div>
        </div>

        {{-- Подключаем ПОИСК обращений и заказов по номеру телефона --}}
        @include('leads.autofind-lead-script')



