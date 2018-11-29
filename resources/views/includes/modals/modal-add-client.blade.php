<div class="reveal" id="add-client" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>НОВЫЙ КЛИЕНТ</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-client-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <fieldset>
                <legend>
                    <div class="switch alt-switch tiny">
                        {{ Form::checkbox('private_status', 2, $lead->private_status, ['id'=>'private_status', 'class' =>'switch-input']) }}

                        <label class="switch-paddle" for="private_status" data-toggle="lead-info-company lead-info-private lead-info-bank">
                            <span class="show-for-sr">Компания?</span>
                            <span class="switch-active" aria-hidden="true" title="Физическое лицо"></span>
                            <span class="switch-inactive" aria-hidden="true" title="Юридическое лицо"></span>
                        </label>

                        <span id="title-switch-company-private">
                            @if($lead->private_status == 1)
                                Компания
                            @else
                                Физическое лицо
                            @endif
                        </span>
                    </div>
                </legend>

                <div class="grid-x grid-padding-x" id="wrap-company-private">

                    <script>
                        $('#wrap-company-private').on('on.zf.toggler', function() {
                            $('#title-switch-company-private').text("Компания");
                            $('#private_status').val(1);

                            // Включаем обязательное заполнение
                            $('[name=company_name]').attr('required', 'required');
                            $('[name=inn]').attr('required', 'required');

                            $('[name=passport_number]').removeAttr('required');
                            $('[name=passport_date]').removeAttr('required');
                            $('[name=passport_released]').removeAttr('required');
                            // alert('На компанию');
                            // $('.passport_address').removeAttr('required');

                        });

                        $('#wrap-company-private').on('off.zf.toggler', function() {
                            $('#title-switch-company-private').text("Физическое лицо");
                            $('#private_status').val(2);

                            // Включаем обязательное заполнение
                            $('[name=passport_number]').attr('required', 'required');
                            $('[name=passport_date]').attr('required', 'required');
                            $('[name=passport_released]').attr('required', 'required');
                            // $('.passport_address').attr('required', 'required');

                            // Выключаем обязательное заполнение
                            $('[name=company_name]').removeAttr('required');
                            $('[name=inn]').removeAttr('required');
                            // alert('На юзера');

                        });
                    </script>

                    <div class="small-12 medium-6 cell">
                        <label>Фамилия
                            @include('includes.inputs.name', ['name'=>'second_name', 'value'=>$new_user->second_name, 'required'=>'required'])
                        </label>
                        <label>Имя
                            @include('includes.inputs.name', ['name'=>'first_name', 'value'=>$new_user->first_name, 'required'=>'required'])
                        </label>
                        <label>Отчество
                            @include('includes.inputs.name', ['name'=>'patronymic', 'value'=>$new_user->patronymic, 'required'=>'required'])
                        </label>
                        <label>Почта
                            @include('includes.inputs.email', ['value'=>$new_user->email, 'name'=>'email', 'required'=>'required'])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell lead-info-company" id="lead-info-company" data-toggler="switch-on">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">
                                <label>Компания
                                    @include('includes.inputs.string', ['name'=>'company_name', 'value'=>$new_company->company_name, 'required'=>''])
                                </label>
                            </div>

                            <div class="small-12 cell">
                                <label>ИНН
                                    @include('includes.inputs.inn', ['value'=>$new_user->inn, 'name'=>'inn', 'required'=>''])
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>КПП
                                    @include('includes.inputs.kpp', ['value'=>$new_user->kpp, 'name'=>'kpp', 'required'=>''])
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="small-12 medium-6 cell lead-info-private" id="lead-info-private" data-toggler="switch-off">
                        <div class="grid-x grid-padding-x">
                          <div class="small-12 cell">
                            <label>Паспорт (серия, номер)
                              @include('includes.inputs.passport_number', ['name'=>'passport_number', 'value'=>$new_user->passport_number, 'required'=>'required'])
                            </label>
                          </div>
                          <div class="small-12 cell">
                            <label>Когда выдан
                              @include('includes.inputs.date', ['name'=>'passport_date', 'value'=>$new_user->passport_date, 'required'=>'required'])
                            </label>
                          </div>
                        </div>
                        <div class="grid-x grid-padding-x">
                          <div class="small-12 cell">
                            <label>Кем выдан
                              {{ Form::text('passport_released', $new_user->passport_released, ['class'=>'varchar-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'required'=>'required']) }}
                            </label>
                          </div>
                        </div>
                        <div class="grid-x grid-padding-x">
                          <div class="small-12 cell">
                            <label>Адрес прописки
                              @include('includes.inputs.address', ['value'=>$new_user->passport_address, 'name'=>'passport_address', 'required'=>''])
                            </label>
                          </div>
                        </div>
                    </div>

                </div>

                    {{-- Начало блока адреса --}}
                    <div class="small-12 cell lead-info-address" id="lead-info-address">
                        <hr>
                        <div class="grid-x grid-padding-x">
                            {{-- <div class="small-12 cell">
                                <label>Страна
                                    @php
                                        $country_id = null;
                                        if (isset($new_user->location->country_id)) {
                                            $country_id = $new_user->location->country_id;
                                        }
                                    @endphp
                                    {{ Form::select('country_id', $countries_list, $country_id)}}
                                </label>
                            </div> --}}
                            <div class="small-12 medium-4 cell">
                                <label class="input-icon">
                                    @php
                                        $city_name = null;
                                        $city_id = null;
                                        if(isset($new_user->location->city->name)) {
                                            $city_name = $new_user->location->city->name;
                                            $city_id = $new_user->location->city->id;
                                        }
                                    @endphp

                                    @include('includes.inputs.city_search', ['city' => isset($lead->location->city->name) ? $lead->location->city : null, 'id' => 'cityFormModalClient', 'required' => true])

                                </label>
                            </div>
                                <div class="small-12 medium-8 cell">
                                <label>Адрес
                                    @php
                                        $address = null;
                                        if (isset($new_user->location->address)) {
                                            $address = $new_user->location->address;
                                        }
                                    @endphp
                                    @include('includes.inputs.address', ['value'=>$address, 'name'=>'address', 'required'=>''])
                                </label>
                            </div>

                        </div>
                    </div>
                    {{-- Конец блока адреса --}}



                    {{-- Начало блока банковских реквизитов --}}
                    <div class="small-12 cell lead-info-bank" id="lead-info-bank" data-toggler="switch-on">
                        <hr>
                        <div class="grid-x grid-padding-x">

                            <div class="small-12 medium-9 cell">
                                <label>Банк
                                    @include('includes.inputs.bank', ['value'=>$new_user->bank, 'name'=>'bank', 'required'=>''])
                                </label>
                            </div>
                            <div class="small-12 medium-3 cell">
                                <label>БИК
                                    @include('includes.inputs.bic', ['value'=>'', 'name'=>'bank', 'required'=>''])
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                <label>Р/С
                                    @include('includes.inputs.account', ['value'=>$new_user->account_settlement, 'name'=>'account_settlement', 'required'=>''])
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                <label>К/С
                                    @include('includes.inputs.account', ['value'=>$new_user->account_correspondent, 'name'=>'account_correspondent', 'required'=>''])
                                </label>
                            </div>
                        </div>
                    </div>


                    {{-- Конец блока банковских реквизитов --}}

            </fieldset>

        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить клиента', ['class'=>'button modal-button', 'id' => 'submit-add-client']) }}
        </div>
    </div>

    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')






