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
                <a data-tabs-target="content-panel-client" href="#content-panel-client">Коммуникация</a>
            </li>
            @endif

            <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Реквизиты</a></li>
            <li class="tabs-title"><a data-tabs-target="content-panel-4" href="#content-panel-4">График работы</a></li>

            <li class="tabs-title"><a data-tabs-target="content-panel-about" href="#content-panel-about">Описание</a></li>

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
                    <div class="small-10 medium-4 cell">
                        <label>Статус компании
                            @include('includes.inputs.name', ['name' => 'prename'])
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

                    <div class="small-12 medium-6 cell checkbox checkboxer">



                        {{-- Подключаем класс Checkboxer --}}
                        @include('includes.scripts.class.checkboxer')

                        @include('includes.inputs.checker_contragents', [
                            'entity' => $supplier,
                            'title' => 'Производители',
                            'name' => 'manufacturers',
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

                    <div class="small-12 medium-12 cell">
                        @include('includes.selects.loyalties', ['value'=>$client->loyalty_id])
                    </div>

                    <div class="small-12 medium-12 cell">
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


            <!-- Описание компании -->
            <div class="tabs-panel" id="content-panel-about">
                <div class="grid-x grid-padding-x">
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
                        @include('includes.inputs.checker', [
                            'entity' => $company,
                            'model' => 'ProcessesType',
                            'relation'=>'processes_types',
                            'title'=>'Типы услуг'
                        ]
                        )
                    </div>

                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('external_control', 1, null, ['id' => 'external_control']) }}
                        <label for="external_control"><span>Внешний контроль</span></label>
                    </div>


                    {{-- Предлагаем добавить компанию в производители, если, конечно, создаем ее не из под страницы создания производителей --}}

                        @if(empty($manufacturer))
                            @if(isset($company->manufacturer_self))
                                @if($company->manufacturer_self == false)
                                <div class="small-12 cell checkbox">
                                    {{ Form::checkbox('manufacturer_self', 1, $company->manufacturer_self, ['id' => 'manufacturer_self']) }}
                                    <label for="manufacturer_self"><span>Производитель</span></label>
                                </div>
                                @endif
                            @endif
                        @endif

                    {{-- Предлагаем добавить компанию в поставщики, если, конечно, создаем ее не из под страницы создания поставщиков --}}
                    
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
                <!-- Блок дилера -->
                <div class="small-12 medium-6 cell">
                </div>
                <div class="small-12 medium-6 cell">
                    <div class="small-12 large-6 cell">
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
                                        @include('includes.inputs.phone', ['value' => isset($user->main_phone->phone) ? $user->main_phone->phone : null, 'name'=>'main_phone', 'required' => true, 'id' => 'main-phone'])
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
                <!-- Конец блока дилера -->
                @endif



            </div>
        </div>


    </div>

