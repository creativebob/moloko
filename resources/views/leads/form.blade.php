

<div class="grid-x tabs-wrap inputs">
  <div class="small-12 medium-6 large-6 cell">


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
                    <div class="small-6 medium-6 large-6 cell">
                        <label>Контактное лицо
                            @include('includes.inputs.name', ['name'=>'name', 'value'=>$lead->name, 'required'=>'required'])
                        </label>
                    </div>
                    <div class="small-6 medium-6 cell">
                        <label>Телефон
                            @include('includes.inputs.phone', ['value'=>$lead->phone, 'name'=>'phone', 'id'=>'phone' ,'required'=>'required'])
                        </label>

                        <div id="port_autofind">
                        </div>
                        {{-- Подключаем ПОИСК продукции для добавления на сайт --}}
                        @include('leads.autofind-lead-script')

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
                </div>


                {{-- <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-4 large-4 cell">
                        <label>Страна
                            @php
                                $country_id = null;
                                if (isset($lead->location->country_id)) {
                                    $country_id = $lead->location->country_id;
                                }
                            @endphp
                            {{ Form::select('country_id', $countries_list, $country_id)}}
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Почта
                            @include('includes.inputs.email', ['value'=>$lead->email, 'name'=>'email', 'required'=>''])
                        </label> 
                    </div>
                </div>--}}

            </div>
        </div>

        <!-- ЗАКАЗ -->
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 large-12 cell">
                <ul class="tabs-list" data-tabs id="tabs-extra-leads">
                    <li class="tabs-title is-active"><a href="#content-panel-order" aria-selected="true">Заказ</a></li>
                    <li class="tabs-title"><a href="#content-panel-client" aria-selected="true">Клиент</a></li>
                    <li class="tabs-title"><a href="#content-panel-attribution" aria-selected="true">Аттрибуция</a></li>
                </ul>



                {{-- Контент доп таба --}}
                <div data-tabs-content="tabs-extra-leads">

                    {{-- ЗАКАЗ --}}
                    <div class="tabs-panel is-active" id="content-panel-order">

                        <div class="grid-x">
                            <div class="small-12 medium-12 large-12 cell">

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

                            </div>
                        </div>

                    </div>

                    {{-- КЛИЕНТ --}}
                    <div class="tabs-panel" id="content-panel-client">
                        <div class="grid-x grid-padding-x">

                        </div>
                    </div>

                    {{-- АТТРИБУЦИЯ --}}
                    <div class="tabs-panel" id="content-panel-attribution">
                        <div class="grid-x grid-padding-x">

                        </div>
                    </div>

                {{-- Конец контента доп таба --}}
                </div>

            </div>
        </div>

    </div>

    <!-- Правый блок -->
    <div class="small-12 medium-6 large-6 cell">
        <div class="grid-x tabs-right">
            <div class="small-12 cell">
                <ul class="tabs-list" data-tabs id="tabs-leads">
                    <li class="tabs-title is-active"><a href="#content-panel-history" aria-selected="true">События</a></li>
                    <li class="tabs-title"><a href="#content-panel-catalog" aria-selected="true">Каталог</a></li>
                    <li class="tabs-title"><a href="#content-panel-documents" aria-selected="true">Документы</a></li>
                    <li class="tabs-title"><a href="#content-panel-claims" aria-selected="true">Рекламации</a></li>
                    <li class="tabs-title"><a href="#content-panel-measurements" aria-selected="true">Замеры</a></li>
                </ul>
            </div>
        </div>

        <div class="tabs-content tabs-leads" data-tabs-content="tabs-leads">

            {{-- Взаимодействия: задачи и события --}}
            <div class="tabs-panel is-active" id="content-panel-history">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 large-12 cell">
                        <fieldset class="fieldset-access">
                            <legend>Задачи:</legend>
                            <div class="grid-x grid-padding-x"> 
                                <table class="table-challenges" id="table-challenges">
                                    <thead>
                                        <tr>
                                            <th>Задача</th>
                                            <th>Дата</th>
                                            <th>Время</th>
                                            <th>Описание</th>
                                            <th>Исполнитель</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Перезвонить</td>
                                            <td>12.06.2016</td>
                                            <td>13:45</td>
                                            <td>Да, да. Нужно перезвонить клиенту! Срочно!</td>
                                            <td>Виноградова Алена</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </fieldset>
                        <fieldset class="fieldset-access">
                            <legend>События:</legend>
                            <div class="grid-x grid-padding-x"> 
                                <table class="table-notes" id="table-notes">
                                    <thead>
                                        <tr>
                                            <th>Время</th>
                                            <th>Событие</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <span class="note_date">12.06.2016</span>
                                                <br>
                                                <span class="note_time">11:40</span>
                                            </td>
                                            <td>
                                                @include('includes.inputs.varchar', ['name'=>'body', 'value'=>null, 'required'=>''])
                                            </td>
                                            <td><a href="#" class="button">Добавить</a></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="note_date">12.06.2016</span>
                                                <br>
                                                <span class="note_time">11:40</span>
                                            </td>
                                            <td>Кое что интересное мне сказал тут клиент на днях. Говорит в соседней конторе он купил бы эти ворота за сущие гроши. А у нас они стоят еще меньше. Еще меньше, Карл!</td>
                                            <td><a class="icon-delete sprite" data-open="item-delete"></a></td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </fieldset> 
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

            {{-- Документы --}}
            <div class="tabs-panel" id="content-panel-documents">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 large-6 cell">
                    </div>
                </div>
            </div>

            {{-- Рекламации --}}
            <div class="tabs-panel" id="content-panel-claims">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 large-6 cell">
                    </div>
                </div>
            </div>

            {{-- Замеры --}}
            <div class="tabs-panel" id="content-panel-measurements">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 large-6 cell">
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




