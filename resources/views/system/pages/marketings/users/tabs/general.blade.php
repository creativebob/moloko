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

            @isset($employee)

                <input name="user_type" type="hidden" value='1'>

            @else
                <div class="grid-x grid-padding-x">
                    <div class="small-12 cell">
                        <label>Статус пользователя
                            {{ Form::select('user_type', [ '0' => 'Чужой', '1' => 'Свой']) }}
                        </label>
                    </div>

                    <div class="small-12 cell tabs-margin-bottom">
                        <label>
                            @include('includes.selects.filials_for_user', ['value' => $user->filial_id, 'disabled' => $user->exists ? 'true' : null])
                        </label>
                    </div>
                </div>
            @endisset

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <label>Логин
                        {{ Form::text('login', $user->login, ['class'=>'login-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'pattern'=>'[A-Za-z0-9._-]{6,30}']) }}
                        <span class="form-error">Обязательно нужно логиниться!</span>
                    </label>
                    <label>Пароль
                        {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
                        <span
                            class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
                    </label>
                    <label>Пароль повторно
                        {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password-repeat', 'data-equalto' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
                        <span class="form-error">Пароли не совпадают!</span>
                    </label>
                </div>
            </div>
            <div class="grid-x grid-padding-x">

                @if (isset($user->login))
                    @can('index', App\Role::class)
                        <div class="small-12 cell tabs-margin-top">
                            <table class="content-table">
                                <caption>Уровень доступа</caption>
                                <thead>
                                <tr>
                                    <th>Роль</th>
                                    <th>Филиал</th>
                                    <th>Должность</th>
                                    <th>Инфа</th>
                                    <th class="td-delete"></th>
                                </tr>
                                </thead>
                                <tbody class="roleuser-table">
                                @if (!empty($user->role_user))
                                    @foreach ($user->role_user as $role_user)
                                        @include('system.pages.marketings.users.roles', $role_user)
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="small-8 small-offset-2 medium-8 medium-offset-2 tabs-margin-top text-center cell">
                            <a class="button" data-open="role-add">Настройка доступа</a>
                        </div>
                    @endcan
                @endif

                <div class="small-12 text-center cell checkbox">
                    {!! Form::hidden('access_block', 0) !!}
                    {{ Form::checkbox('access_block', 1, $user->access_block == 1, ['id'=>'access-block-checkbox']) }}
                    <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
                </div>

                @if($user->exists && isset($site))
                    {!! Form::hidden('site_id', $site->id) !!}
                @endif

            </div>
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


