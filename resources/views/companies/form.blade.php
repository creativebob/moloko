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
                <a data-tabs-target="content-panel-supplier" href="#content-panel-supplier">Рабочая информация</a>
            </li>
            @endif
            @if(!empty($client))
            <li class="tabs-title">
                <a data-tabs-target="content-panel-client" href="#content-panel-client">Рабочая информация</a>
            </li>
            @endif

            <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Реквизиты</a></li>
            <li class="tabs-title"><a data-tabs-target="content-panel-4" href="#content-panel-4">График работы</a></li>
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
                            @include('includes.inputs.phone', ['value' => isset($company->main_phone->phone) ? $company->main_phone->phone : null, 'name'=>'main_phone'])
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
                        @include('includes.selects.countries', ['value'=>$company->location ? $company->location->country_id : null])
                    </div>


                    <div class="small-12 medium-6 cell">
                        {{-- Город --}}
                        @include('includes.inputs.city_search', ['city' => isset($company->location->city->name) ? $company->location->city : null, 'id' => 'cityForm', 'required' => true])

                        <label>Адрес
                            @include('includes.inputs.address', ['value' => isset($company->location->address) ? $company->location->address : null, 'name'=>'address'])
                        </label>
                    </div>


                    {{-- $manufacturer->getTable() --}}



                </div>
            </div>

            @if(!empty($manufacturer))
            <!-- Блок производителя -->
            <div class="tabs-panel" id="content-panel-manufacturer">
                <div class="grid-x grid-padding-x">
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
                            @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$dealer->description])
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

                    <div class="small-12 medium-6 cell checkbox checkboxer">



                        {{-- Подключаем класс Checkboxer --}}
                        @include('includes.scripts.class.checkboxer')

                        @include('includes.inputs.checker_contragents', [
                            'entity' => $supplier,
                            'title' => 'Производители',
                            'name' => 'manufacturers'
                        ]
                        )

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
                    <div class="small-12 medium-6 cell">
                        <label>Комментарий к клиенту
                            @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$client->description])
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

            <!-- Настройки -->
            <div class="tabs-panel" id="content-panel-5">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 large-6 cell">
                        <label>Алиас
                            @include('includes.inputs.alias', ['value'=>$company->alias, 'name'=>'alias'])
                        </label>
                    </div>
                    <div class="small-12 large-6 cell">
                    </div>

                    @include('includes.scripts.class.checkboxer')

                    <div class="small-12 large-6 cell checkbox checkboxer">

                        @include('includes.scripts.class.checkboxer')
                        @include('includes.inputs.checker', ['entity' => $company, 'model'=>'ServicesType', 'relation'=>'services_types', 'title'=>'Типы услуг'])
                    </div>

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

        </div>
    </div>


</div>

