



  <div class="grid-x tabs-wrap">
    <div class="small-12 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Учетные данные</a></li>
        <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Персональные данные</a></li>
        <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">Представитель компании</a></li>
      </ul>
    </div>
  </div>

  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-5 cell tabs-margin-top">
      <div class="tabs-content" data-tabs-content="tabs">


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

        <!-- Учетные данные -->
        <div class="tabs-panel is-active" id="content-panel-1">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Фамилия
                @include('includes.inputs.text-ru', ['name'=>'second_name', 'value'=>$user->second_name, 'required'=>'required'])
              </label>
              <label>Имя
                @include('includes.inputs.text-ru', ['name'=>'first_name', 'value'=>$user->first_name, 'required'=>'required'])
              </label>
              <label>Отчество
                @include('includes.inputs.text-ru', ['name'=>'patronymic', 'value'=>$user->patronymic, 'required'=>''])
              </label>
            </div>
          </div>

<!--           <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Короткое имя
                {{ Form::text('nickname', $user->nickname, ['class'=>'nickname-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'required', $param]) }}
              </label>
            </div>
          </div> -->

          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$user->phone, 'name'=>'phone', 'required'=>'required'])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$user->extra_phone, 'name'=>'extra_phone', 'required'=>''])
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Почта
              @include('includes.inputs.email', ['value'=>$user->email, 'name'=>'email'])
              </label>         
              <label>Телеграм ID
                {{ Form::text('telegram_id', $user->telegram_id, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите номер Telegram</span>
              </label>
              
            </div>
            <div class="small-12 medium-6 cell">
              <label class="input-icon">Введите город
                @php
                  $city_name = null;
                  $city_id = null;
                  if(isset($user->city->city_name)) {
                    $city_name = $user->city->city_name;
                    $city_id = $user->city->city_id;
                  }
                @endphp
                @include('includes.inputs.city_name', ['value'=>$city_name, 'name'=>'city_name', 'required'=>'required'])
                @include('includes.inputs.city_id', ['value'=>$city_id, 'name'=>'city_id'])
              </label>
              <label>Адрес
                @include('includes.inputs.address', ['value'=>$user->address, 'name'=>'address'])
              </label>
            </div>
          </div>
        </div>
        <!-- Персональные данные -->
        <div class="tabs-panel" id="content-panel-2">
          <div class="grid-x grid-padding-x">
            <div class="small-5 medium-4 cell">
              <label>Дата рождения
                @include('includes.inputs.date', ['name'=>'birthday', 'value'=>$user->birthday, 'required'=>''])
              </label>
            </div>
            <div class="small-6 small-offset-1 medium-6 medium-offset-2 cell radiobutton">Пол<br>
              {{ Form::radio('sex', '1', true, ['id'=>'man']) }}
              <label for="man"><span>Мужской</span></label>
              {{ Form::radio('sex', '0', false, ['id'=>'woman']) }}
              <label for="woman"><span>Женский</span></label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Паспорт (серия, номер)
              {{ Form::text('passport_number', $user->passport_number, ['class'=>'passport-number-field', 'pattern'=>'[0-9]{2} [0-9]{2} №[0-9]{6}', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-5 medium-6 cell">
              <label>Когда выдан
                @include('includes.inputs.date', ['name'=>'passport_date', 'value'=>$user->passport_date, 'required'=>''])
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
              <label>Кем выдан
              {{ Form::text('passport_released', $user->passport_released, ['class'=>'varchar-field passport-released-field', 'maxlength'=>'60', 'autocomplete'=>'off', 'pattern'=>'[А-Яа-яЁё -\.]{60}']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Адрес прописки
                @include('includes.inputs.address', ['value'=>$user->passport_address, 'name'=>'passport_address'])
              </label>
            </div>
          </div>
        </div>
        <!-- Представитель компании -->
        <div class="tabs-panel" id="content-panel-3">
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
                @include('includes.inputs.account_settlement', ['value'=>$user->account_settlement, 'name'=>'account_settlement'])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>К/С
                @include('includes.inputs.account_correspondent', ['value'=>$user->account_correspondent, 'name'=>'account_correspondent'])
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="small-12 medium-5 medium-offset-1 large-5 large-offset-2 cell">
      <fieldset class="fieldset-access">
        <legend>Настройка доступа</legend>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell">
            <label>Статус пользователя
              {{ Form::select('user_type', [ '1' => 'Пользователь системы', '2' => 'Клиент']) }}
            </label>
          </div>

          @if(!empty($filials_list))
          <div class="small-12 cell">
            <label>Для филиала
              {{ Form::select('filial_id', $filials_list, null) }}
            </label>
          </div>
          @endif

        </div>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell tabs-margin-top">
            <label>Логин
              {{ Form::text('login', $user->login, ['class'=>'login-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'required', 'pattern'=>'[A-Za-z0-9._-]{6,30}']) }}
              <span class="form-error">Обязательно нужно логиниться!</span>
            </label>
            <label>Пароль
              {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
              <span class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
            </label>
            <label>Пароль повторно
              {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '30', 'id' => 'password-repeat', 'data-equalto' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
              <span class="form-error">Пароли не совпадают!</span>
            </label>
          </div>
        </div>
        <div class="grid-x grid-padding-x">
          @if ($form == 1)
          <div class="small-12 cell tabs-margin-top">

            <table class="table-content">
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
                @if(!empty($role_users))
                @foreach ($role_users as $role_user)
                  <tr class="item" id="roleuser-{{ $role_user->id }}" data-name="{{ $role_user->role->role_name }}">
                    <td>{{ $role_user->role->role_name }}</td>
                    <td>{{ $role_user->department->department_name }}</td>
                    <td>
                      @if (isset($role_user->position->position_name))
                        {{ $role_user->position->position_name }}
                      @else
                        Спецправо
                      @endif
                    </td>
                    <td>Инфа</td>
                    <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td>
                  </tr>
                @endforeach
                @endif

              </tbody>
            </table>
           
          </div>
          <div class="small-8 small-offset-2 medium-8 medium-offset-2 tabs-margin-top text-center cell">
            <a class="button" data-open="role-add">Настройка доступа</a>
          </div>
          @endif
          <div class="small-12 text-center cell checkbox">
              {{ Form::checkbox('access_block', 1, $user->access_block == 1, ['id'=>'access-block-checkbox']) }}
            <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
          </div>
        </div>
      </fieldset> 
    </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $user)
      @if ($user->moderation == 1)
        <div class="small-12 small-text-center cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$user->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $user)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$user->system_item, 'name'=>'system_item'])
      </div>
    @endcan    

    <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>


  

