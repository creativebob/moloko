<div class="reveal" id="add-client" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>НОВЫЙ КЛИЕНТ</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-client-add', 'data-abide', 'novalidate']) }}
    <input type="hidden" name="lead_type_id" value="{{ $lead->lead_type_id }}">
    <input type="hidden" name="lead_id" value="{{ $lead->id }}">

    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <fieldset>
                <legend>
                
                    <div class="switch alt-switch tiny">
                        {{ Form::checkbox('private_status', null, $lead->private_status, ['id'=>'private_status', 'class' =>'switch-input']) }}

                        <label class="switch-paddle" for="private_status" data-toggle="lead-info-company lead-info-private lead-info-bank">
                            <span class="show-for-sr">Компания?</span>
                            <span class="switch-active" aria-hidden="true" title="Физическое лицо"></span>
                            <span class="switch-inactive" aria-hidden="true" title="Юридическое лицо"></span>
                        </label>

                        <span id="title-switch-company-private">
                            @if($lead->private_status == 1) Компания @else Физическое лицо @endif
                        </span>
                    </div>
                </legend>

                <div class="grid-x grid-padding-x" id="wrap-company-private">

                    @include('clients.modals.scripts.toggler-validation')

                    <div class="small-12 medium-6 cell">
                        <label>Фамилия
                            @include('includes.inputs.name', ['name'=>'second_name', 'value'=>$new_user->second_name, 'required' => true])
                        </label>
                        <label>Имя
                            @include('includes.inputs.name', ['name'=>'first_name', 'value'=>$new_user->first_name, 'required' => true])
                        </label>
                        <label>Отчество
                            @include('includes.inputs.name', ['name'=>'patronymic', 'value'=>$new_user->patronymic, 'required' => true])
                        </label>
                      <label>Телефон
                        @include('includes.inputs.phone', ['value' => isset($new_user->main_phone->phone) ? $new_user->main_phone->phone : null, 'name'=>'main_phone', 'required' => true, 'id' => 'main-phone'])
                      </label>
                        <label>Почта
                            @include('includes.inputs.email', ['value'=>$new_user->email, 'name'=>'email'])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell lead-info-company @if($lead->private_status) switch-on @endif" id="lead-info-company" data-toggler="switch-on">
                        <div class="grid-x grid-padding-x">
                            <div class="small-12 cell">
                                <label>Компания
                                    @include('includes.inputs.string', ['name'=>'company_name', 'value'=>$new_company->company_name])
                                </label>
                            </div>

                            <div class="small-12 cell">
                                <label>ИНН
                                    @include('includes.inputs.inn', ['value'=>$new_user->inn, 'name'=>'inn'])
                                </label>
                            </div>
                            <div class="small-12 cell">
                                <label>КПП
                                    @include('includes.inputs.kpp', ['value'=>$new_user->kpp, 'name'=>'kpp'])
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="small-12 medium-6 cell lead-info-private @if($lead->private_status) switch-off @endif" id="lead-info-private" data-toggler="switch-off">
                        <div class="grid-x grid-padding-x">
                          <div class="small-12 cell">
                            <label>Паспорт (серия, номер)
                              @include('includes.inputs.passport_number', ['name'=>'passport_number', 'value'=>$new_user->passport_number])
                            </label>
                          </div>
                          <div class="small-12 cell">
                            <label>Когда выдан
                              @include('includes.inputs.date', ['name'=>'passport_date', 'value'=>$new_user->passport_date])
                            </label>
                          </div>
                        </div>
                        <div class="grid-x grid-padding-x">
                          <div class="small-12 cell">
                            <label>Кем выдан
                                @include('includes.inputs.varchar', ['name'=>'passport_released', 'value'=>$new_user->passport_released, 'maxlength'=>'60', 'autocomplete'=>'off'])
                            </label>
                          </div>
                        </div>
                        <div class="grid-x grid-padding-x">
                          <div class="small-12 cell">
                            <label>Адрес прописки
                              @include('includes.inputs.address', ['value'=>$new_user->passport_address, 'name'=>'passport_address'])
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
                                    @include('includes.inputs.address', ['value'=>$address, 'name'=>'address'])
                                </label>
                            </div>

                        </div>
                    </div>
                    {{-- Конец блока адреса --}}

                    {{-- Начало блока банковских реквизитов --}}
                    <div class="small-12 cell lead-info-bank @if($lead->private_status) switch-on @endif" id="lead-info-bank" data-toggler="switch-on">
                        <hr>
                        <div class="grid-x grid-padding-x">

                            <div class="small-12 medium-9 cell">
                                <label>Банк
                                    @include('includes.inputs.bank', ['value'=>$new_user->bank, 'name'=>'bank'])
                                </label>
                            </div>
                            <div class="small-12 medium-3 cell">
                                <label>БИК
                                    @include('includes.inputs.bic', ['value'=>'', 'name'=>'bank'])
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                <label>Р/С
                                    @include('includes.inputs.account', ['value'=>$new_user->account_settlement, 'name'=>'account_settlement'])
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                <label>К/С
                                    @include('includes.inputs.account', ['value'=>$new_user->account_correspondent, 'name'=>'account_correspondent'])
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






