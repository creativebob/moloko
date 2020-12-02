<div class="grid-x">
    <div class="cell small-12 medium-6 large-5">

        <div class="grid-x grid-padding-x">
            <div class="small-12 large-6 cell">
                <label>Фамилия
                    @include('includes.inputs.name', ['name'=>'second_name', 'value'=>$user->second_name, 'required' => true])
                </label>
                <label>Имя
                    @include('includes.inputs.name', ['name'=>'first_name', 'value'=>$user->first_name, 'required' => true])
                </label>
                <label>Отчество
                    @include('includes.inputs.name', ['name'=>'patronymic', 'value'=>$user->patronymic])
                </label>
            </div>
            <div class="small-12 large-6 cell">

                @if($user->nickname != null)
                    <label>Временное имя
                        @include('includes.inputs.name', ['name'=>'nickname', 'value'=>$user->nickname])
                    </label>
                @endif

                <label>Выберите аватар
                    {{ Form::file('photo') }}
                </label>

                <div class="text-center">
                    <img id="photo" src="{{ getPhotoPath($user) }}">
                </div>
            </div>
        </div>

        {{--<div class="grid-x grid-padding-x">
        <div class="small-12 medium-6 cell">
        <label>Короткое имя
        {{ Form::text('nickname', $user->nickname, ['class'=>'nickname-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'required']) }}
        </label>
        </div>
        </div>--}}

        <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
                <label>Телефон
                    @include('includes.inputs.phone', ['value' => isset($user->main_phone->phone) ? $user->main_phone->phone : null, 'name'=>'main_phone', 'required' => true, 'id' => 'main-phone'])
                </label>
            </div>
            <div class="small-12 medium-6 cell" id="extra-phones">
            @if (count($user->extra_phones) > 0)
                @foreach ($user->extra_phones as $extra_phone)
                    @include('includes.extra-phone', ['extra_phone' => $extra_phone])
                @endforeach
            @else
                @include('includes.extra-phone')
            @endif
            <!-- <span id="add-extra-phone">Добавить номер</span> -->

            </div>
        </div>

        <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
                <label>
                    @include('includes.selects.countries', ['value'=>isset($user->location) ? $user->location->country_id : null])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                @include('system.common.includes.city_search', ['item' => isset($user->location) ? $user : auth()->user(), 'required' => true])
            </div>
            <div class="small-12 medium-6 cell">
                <label>Адрес
                    @include('includes.inputs.address', ['value' => optional($user->location)->address, 'name' => 'address'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>Почта
                    @include('includes.inputs.email', ['value'=>$user->email, 'name'=>'email'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>Телеграм ID
                    {{ Form::text('telegram', $user->telegram, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
                    <span class="form-error">Укажите номер Telegram</span>
                </label>
            </div>

            <div class="small-12 medium-6 cell">
                <label>Метка пользователя (литер):
                    @include('includes.inputs.string', ['name'=>'liter', 'value'=>$user->liter])
                </label>
            </div>
        </div>
    </div>

    <div class="cell small-12 medium-5 medium-offset-1 large-5 large-offset-2">
        <fieldset class="fieldset-access">
            <legend>Настройка доступа</legend>
            <div class="grid-x grid-padding-x">
                @empty($employee)
                    <div class="cell small-12 tabs-margin-bottom">
                        <label>
                            @include('includes.selects.filials_for_user', ['value' => $user->filial_id, 'disabled' => $user->exists ? 'true' : null])
                        </label>
                    </div>
                @endempty

                @isset($client)
                    @isset($sites)
                        @include('system.pages.clients.includes.access.sites')
                        @include('system.pages.marketings.users.includes.access.access')
                    @endisset
                @else
                    @include('system.pages.marketings.users.includes.access.access')
                @endisset

                @isset($employee)
                    @can('index', App\Role::class)
                        @include('system.pages.hr.employees.includes.access.roles')
                    @endcan
                @endisset
            </div>
            @isset($site)
                {!! Form::hidden('site_id', $site->id) !!}
            @endisset
        </fieldset>
    </div>

    <!-- Представитель компании -->
    {{-- <div class="tabs-panel" id="content-panel-3">
        <div class="grid-x grid-padding-x">
            <div class="small-12 cell checkbox">
                {{ Form::checkbox('orgform_status', 1, $user->orgform_status==1, ['id'=>'orgform-status-checkbox']) }}
                <label for="orgform-status-checkbox"><span>Директор компании (Юридическое лицо)</span></label>
            </div>
        </div>
        <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
                <label>Название компании
                    {{ Form::text('company_name', $user->company_name, ['class'=>'varchar-field company-name-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'pattern'=>'[A-Za-zА-Яа-яЁё0-9.,_-/\s()]{3,40}']) }}
                </label>
            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
                <label>ИНН
                    @include('includes.inputs.inn', ['value'=>$user->inn, 'name'=>'inn'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>КПП
                    @include('includes.inputs.kpp', ['value'=>$user->kpp, 'name'=>'kpp'])
                </label>
            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
                <label>Банк
                    @include('includes.inputs.bank', ['value'=>$user->bank, 'name'=>'bank'])
                </label>
            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
                <label>Р/С
                    @include('includes.inputs.account', ['value'=>$user->account_settlement, 'name'=>'account_settlement'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>К/С
                    @include('includes.inputs.account', ['value'=>$user->account_correspondent, 'name'=>'account_correspondent'])
                </label>
            </div>
        </div>
    </div> --}}
</div>


