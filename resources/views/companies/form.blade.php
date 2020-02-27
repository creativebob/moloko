<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a aria-selected="true" href="#content-panel-1">Общая информация</a></li>

            {{-- Подключаемые специфические разделы --}}
            @if(!empty($manufacturer))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-manufacturer" href="#content-panel-manufacturer">Рабочая информация</a>
            </li>
            @endif
            @if(!empty($dealer))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-dealer" href="#content-panel-dealer">Рабочая информация</a>
            </li>
            @endif
            @if(!empty($supplier))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-supplier" href="#content-panel-supplier">Информация о поставщике</a>
            </li>
            @endif
            @if(!empty($client))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-client" href="#content-panel-client">О клиенте</a>
            </li>
            @endif

            <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Реквизиты</a></li>
            <li class="tabs-title"><a data-tabs-target="content-panel-about" href="#content-panel-about">Описание</a></li>
            <li class="tabs-title"><a data-tabs-target="content-panel-4" href="#content-panel-4">График работы</a></li>
            <li class="tabs-title"><a data-tabs-target="content-panel-brand" href="#content-panel-brand">Брендирование</a></li>
            <li class="tabs-title"><a data-tabs-target="content-panel-5" href="#content-panel-5">Настройка</a></li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <!-- Общая информация -->
            <div class="tabs-panel is-active" id="content-panel-1">
                <div class="grid-x grid-padding-x">
                    <div class="small-2 medium-2 cell">
                        @include('includes.selects.legal_forms', ['value'=>$company->legal_form_id])
                    </div>
                    <div class="small-10 medium-4 cell">
                        <label>Название компании
                            @include('includes.inputs.name', ['value'=>$company->name, 'required' => true])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        {{-- Селект с секторами (Вид деятельности компании) --}}
                        <label>Вид деятельности компании
                            @include('includes.selects.sectors_select', ['sector_id' => $company->sector_id])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Телефон
                            @include('includes.inputs.phone', ['value' => isset($company->main_phone->phone) ? $company->main_phone->phone : null, 'name'=>'main_phone', 'required' => true])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell" id="extra-phones">
                        @if (count($company->extra_phones) > 0)
                        @foreach ($company->extra_phones as $extra_phone)
                        @include('includes.extra-phone', ['extra_phone' => $extra_phone])
                        @endforeach
                        @else
                        @include('includes.extra-phone')
                        @endif

                        <!-- <span id="add-extra-phone">Добавить номер</span> -->
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Почта
                            @include('includes.inputs.email', ['value'=>$company->email, 'name'=>'email'])
                        </label>
                        {{-- Город --}}
                        @include('system.common.includes.city_search', ['item' => $company, 'required' => true])

                    </div>


                    <div class="small-12 medium-6 cell">
                        @include('includes.selects.countries', ['value'=>$company->location ? $company->location->country_id : null])

                        <label>Адрес
                            @include('includes.inputs.address', ['value' => isset($company->location->address) ? $company->location->address : null, 'name'=>'address'])
                        </label>
                    </div>
                    <div class="small-12 medium-3 cell">
                        <label>Почтовый индекс
                            @include('includes.inputs.zip_code', ['value'=>isset($company->location->zip_code) ? $company->location->zip_code : null, 'name'=>'zip_code'])
                        </label>
                    </div>

                    {{-- $manufacturer->getTable() --}}



                </div>
            </div>

            @if(!empty($manufacturer))
            <!-- Блок производителя -->
            <div class="tabs-panel" id="content-panel-manufacturer">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('is_partner', 1, $manufacturer->is_partner, ['id' => 'is_partner-checkbox']) }}
                        <label for="is_partner-checkbox"><span>Партнер</span></label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к производителю
                            @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$manufacturer->description])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока производителя -->
            @endif

            @if(!empty($dealer))
            <!-- Блок дилера -->
            <div class="tabs-panel" id="content-panel-dealer">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к дилеру
                            @include('includes.inputs.textarea', ['name'=>'description_dealer', 'value'=>$dealer->description_dealer])
                        </label>
                    </div>
                    <div class="small-6 medium-3 cell">
                        <label>Скидка
                            @include('includes.inputs.digit', ['name'=>'discount', 'value'=>$dealer->discount])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока дилера -->
            @endif

            @if(!empty($supplier))
            <!-- Блок поставщика -->
            <div class="tabs-panel" id="content-panel-supplier">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-12 cell checkbox checkboxer">
                        {{-- Подключаем класс Checkboxer --}}
                        @include('includes.scripts.class.checkboxer')

                        @include('includes.inputs.checker_contragents', [
                            'entity' => $supplier,
                            'title' => 'Производители',
                            'name' => 'manufacturers',
                        ]
                        )
                    </div>

                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('is_partner', 1, $supplier->is_partner, ['id' => 'is_partner-checkbox']) }}
                        <label for="is_partner-checkbox"><span>Партнер</span></label>
                    </div>

                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('is_partner', 1, $supplier->is_partner, ['id' => 'is_partner-checkbox']) }}
                        <label for="is_partner-checkbox"><span>Партнер</span></label>
                    </div>

                    <div class="small-12 medium-6 cell checkbox">
                        {{ Form::checkbox('preorder', 1, $supplier->preorder, ['id' => 'preorder-checkbox']) }}
                        <label for="preorder-checkbox"><span>Предзаказ</span></label>
                    </div>


                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к поставщику
                            @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$supplier->description])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока поставщика -->
            @endif

            @if(!empty($client))

            <!-- Блок клиента -->
            <div class="tabs-panel" id="content-panel-client">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-12 cell">
                        @include('includes.selects.loyalties', ['value'=>$client->loyalty_id])
                    </div>

                    <div class="small-12 medium-12 cell">
                        <label>Комментарий к клиенту
                            @include('includes.inputs.textarea', ['name'=>'description_client', 'value'=>$client->description])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец блока клиента -->

            @endif


            <!-- Реквизиты -->
            <div class="tabs-panel" id="content-panel-2">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-6 cell">
                        <label>ИНН
                            @include('includes.inputs.inn', ['value'=>$company->inn, 'name'=>'inn'])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>КПП
                            @include('includes.inputs.kpp', ['value'=>$company->kpp, 'name'=>'kpp'])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>ОГРН
                            @include('includes.inputs.ogrn', ['value'=>$company->ogrn, 'name'=>'ogrn'])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>ОКПО
                            @include('includes.inputs.okpo', ['value'=>$company->okpo, 'name'=>'okpo'])
                        </label>
                    </div>

                    <div class="small-12 cell" id="bank-accounts-list">

                        {{-- Подключаем банковские аккаунты --}}
                        @include('includes.bank_accounts.fieldset', ['company' => $company])

                    </div>

                </div>


            </div>
            <!-- Конец реквизиты -->


            <!-- Описание компании -->
            <div class="tabs-panel" id="content-panel-about">
                <div class="grid-x grid-padding-x">

                    {{--

                    @include('includes.scripts.class.checkboxer')

                    <div class="small-12 large-6 cell checkbox checkboxer">

                        @include('includes.scripts.class.checkboxer')
                        @include('includes.inputs.checker', [
                            'entity' => $company,
                            'model' => 'ProcessesType',
                            'relation'=>'processes_types',
                            'title'=>'Типы услуг'
                        ]
                        )
                    </div>

                    --}}

                    <div class="small-6 medium-3 cell">
                        <label>Дата рождения компании
                            @include('includes.inputs.date', ['name'=>'birthday_company', 'value'=> isset($company->birthday_company) ? $company->birthday_company->format('d.m.Y') : null ])
                        </label>
                    </div>
                    <div class="small-6 cell">

                    </div>

                    <div class="small-12 medium-12 cell">
                        <label>Информация о компании:
                            {{ Form::textarea('about', $company->about, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                        </label><br>
                    </div>
                    <div class="small-12 medium-12 cell">
                        <label>Description (Описание для SEO)
                            @include('includes.inputs.textarea', ['name'=>'seo_description', 'value'=>$company->seo_description])
                        </label>
                    </div>
                </div>
            </div>
            <!-- Конец описания компании -->



            <!-- Брендирование -->
            <div class="tabs-panel" id="content-panel-brand">
                <div class="grid-x grid-padding-x">

                    <photo-upload-component :options='@json(['title' => 'Стандартный логотип (jpg или png)', 'name' => 'photo'])' :photo='@json($company->photo)'></photo-upload-component>
                    <photo-upload-component :options='@json(['title' => 'Белый логотип (svg)', 'name' => 'white'])' :photo='@json($company->white)'></photo-upload-component>
                    <photo-upload-component :options='@json(['title' => 'Черный логотип (svg)', 'name' => 'black'])' :photo='@json($company->black)'></photo-upload-component>
                    <photo-upload-component :options='@json(['title' => 'Цветной логотип (svg)', 'name' => 'color'])' :photo='@json($company->color)'></photo-upload-component>

{{--                    <div class="small-12 cell">--}}
{{--                        <table class="brand-logo-table">--}}
{{--                            <tbody class="brand-logo-table-tbody">--}}
{{--                                <tr>--}}
{{--                                    <td>--}}
{{--                                        <label>Стандартный логотип (jpg или png)--}}
{{--                                            {{ Form::file('photo') }}--}}
{{--                                        </label>--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        <img id="photo" src="{{ getPhotoPath($company, 'small') }}">--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>--}}
{{--                                        <label>Белый логотип (svg)--}}
{{--                                            {{ Form::file('logo_white') }}--}}
{{--                                        </label>--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        <img width="100px" id="photo" src="/img/system/svg/logo-white.svg">--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>--}}
{{--                                        <label>Черный логотип (svg)--}}
{{--                                            {{ Form::file('logo_black') }}--}}
{{--                                        </label>--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        <img width="100px" id="photo" src="/img/system/svg/logo-black.svg">--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>--}}
{{--                                        <label>Цветной логотип (svg)--}}
{{--                                            {{ Form::file('logo_color') }}--}}
{{--                                        </label>--}}
{{--                                    </td>--}}
{{--                                    <td>--}}
{{--                                        <img width="100px" id="photo" src="/img/system/svg/logo-color.svg">--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    </div>--}}

                </div>
            </div>


            <!-- Настройки -->
            <div class="tabs-panel" id="content-panel-5">
                <div class="grid-x grid-padding-x">

                    <div class="small-6 medium-6 cell">
                        <label>Коммерческое обозначение
                            @include('includes.inputs.name', ['name' => 'designation'])
                        </label>
                    </div>
                    <div class="small-6 medium-6 cell">
                        <label>Статус по виду деятельности
                            @include('includes.inputs.name', ['name' => 'prename'])
                        </label>
                    </div>

                    <div class="small-12 large-6 cell">
                        <label>Название компании (короткий вариант)
                            @include('includes.inputs.name', ['value'=>$company->name_short, 'name'=>'name_short'])
                        </label>
                    </div>

                    <div class="small-12 large-6 cell">
                        <label>Алиас
                            @include('includes.inputs.alias', ['value'=>$company->alias, 'name'=>'alias'])
                        </label>
                    </div>

                    <div class="small-12 cell">
                        <label>Слоган
                            @include('includes.inputs.name', ['name' => 'slogan'])
                        </label>
                    </div>

                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('external_control', 1, null, ['id' => 'external_control']) }}
                        <label for="external_control"><span>Внешний контроль</span></label>
                    </div>


                    {{-- Предлагаем добавить компанию в производители, если, конечно, создаем ее не из под страницы создания производителей --}}
                    @can('index', App\Manufacturer::class)
                        @if(empty($manufacturer))
                            @if(isset($company->manufacturer_self))
                                @if($company->manufacturer_self == false)
                                <div class="small-12 cell checkbox">
                                    {{ Form::checkbox('manufacturer_self', 1, $company->manufacturer_self, ['id' => 'manufacturer_self']) }}
                                    <label for="manufacturer_self"><span>Производитель</span></label>
                                </div>
                                @endif

                                    @if(isset($supplier))
                                        <div class="small-12 cell checkbox">
                                            {!! Form::hidden('is_vendor', 0) !!}
                                            {!! Form::checkbox('is_vendor', 1, isset($supplier->vendor), ['id' => 'checkbox-is_vendor']) !!}
                                            <label for="checkbox-is_vendor"><span>Продавец</span></label>
                                        </div>
                                    @endif

                            @endif
                        @endif
                    @endcan

                    {{-- Предлагаем добавить компанию в поставщики, если, конечно, создаем ее не из под страницы создания поставщиков --}}
                    @can('index', App\Supplier::class)
                        @if(empty($supplier))
                            @if(isset($company->supplier_self) && (Auth::user()->company_id != null))
                                @if($company->supplier_self == false)
                                <div class="small-12 cell checkbox">
                                    {{ Form::checkbox('supplier_self', 1, $company->supplier_self, ['id' => 'supplier_self']) }}
                                    <label for="supplier_self"><span>Поставщик</span></label>
                                </div>
                                @endif

                            @endif
                        @endif
                    @endcan

                        {{-- Чекбоксы управления --}}
                        @include('includes.control.checkboxes', ['item' => $company])
                    </div>
                </div>
                <!-- Конец настройки -->


                <!-- График работы -->
                <div class="tabs-panel" id="content-panel-4">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 medium-6 cell">

                            @include('includes.inputs.schedule', ['worktime'=>$company->worktime])

                        </div>
                    </div>
                </div>
                <!-- Конец график работы -->

            </div>

            <div class="small-12 medium-5 large-7 cell tabs-margin-top">
            </div>

            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit($submitButtonText, ['class'=>'button']) }}
            </div>
        </div>

        <div class="small-12 medium-5 large-7 cell text-left tabs-margin-top">
            <div class="grid-x grid-padding-x">

                @if(!empty($dealer))
                <!-- Блок дилера -->
                <div class="small-12 medium-6 cell">

                </div>
                <!-- Конец блока дилера -->
                @endif



                @if(!empty($user))
                <div class="small-12 medium-1 large-6 cell">
                </div>

                <div class="small-12 medium-11 large-6 cell">

                        <fieldset>
                            <legend>Директор (руководитель)</legend>
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 cell">
                                    <label>Фамилия
                                        @include('includes.inputs.name', ['name'=>'second_name', 'value'=>$user->second_name, 'required' => true])
                                    </label>
                                </div>
                                <div class="small-12 cell">
                                    <label>Имя
                                        @include('includes.inputs.name', ['name'=>'first_name', 'value'=>$user->first_name, 'required' => true])
                                    </label>
                                </div>
                                <div class="small-12 cell">
                                    <label>Отчество
                                        @include('includes.inputs.name', ['name'=>'patronymic', 'value'=>$user->patronymic])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Телефон
                                        @include('includes.inputs.phone', ['value' => isset($user->main_phone->phone) ? $user->main_phone->phone : null, 'name'=>'user_phone', 'required' => true, 'id' => 'main-phone'])
                                    </label>
                                </div>

                                <input type="hidden" name="user_country_id" value="1">

                                <div class="small-12 medium-6 cell">
                                    @php

                                    @endphp
                                    @include('system.common.includes.city_search', ['item' => isset($user->location) ? $user : $auth_user, 'required' => true, 'name' => 'user_city_id'])
{{--                                    @php isset(Auth::user()->location->city->name) ? $city_default = Auth::user()->location->city : $city_default = null; @endphp--}}
{{--                                    @include('includes.inputs.city_search', ['city' => isset($user->location->city->name) ? $user->location->city : $city_default, 'id' => 'cityForm2', 'required' => true, 'field_name' => 'user_city_id'])--}}
                                </div>

                                <div class="small-12 medium-12 cell">
                                    <label>Адрес
                                        @php
                                            $address = null;
                                            if (isset($user->location->address)) {
                                                $address = $user->location->address;
                                            }
                                        @endphp
                                        @include('includes.inputs.address', ['value'=>$address, 'name'=>'user_address'])
                                    </label>
                                </div>

                            </div>
                        </fieldset>
                    </div>
                @endif



            </div>
        </div>


    </div>

