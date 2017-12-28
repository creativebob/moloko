



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
              {{ Form::text('first_name', $user->first_name, ['class'=>'first-name-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
              <label>Имя
              {{ Form::text('second_name', $user->second_name, ['class'=>'second-name-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
              <label>Отчество
              {{ Form::text('patronymic', $user->patronymic, ['class'=>'patronymic-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
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
                {{ Form::text('phone', $user->phone, ['class'=>'phone-field', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off', 'required']) }}
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Телефон
                {{ Form::text('extra_phone', $user->extra_phone, ['class'=>'phone-field', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off']) }}
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Почта
                {{ Form::text('email', $user->email, ['class'=>'email-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите почту</span>
              </label>
              <label>Телеграм ID
                {{ Form::text('telegram_id', $user->telegram_id, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите номер Telegram</span>
              </label>
              
            </div>
            <div class="small-12 medium-6 cell">
              <label class="input-icon">Город
                <input type="text" name="id_city" autocomplete="off">
                <div class="sprite-input icon-password"></div>
              </label>
              <label>Адрес
              {{ Form::text('address', $user->address, ['class'=>'address-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
        </div>
        <!-- Персональные данные -->
        <div class="tabs-panel" id="content-panel-2">
          <div class="grid-x grid-padding-x">
            <div class="small-5 medium-4 cell">
              <label>Дата рождения
              {{ Form::text('birthday', $user->birthday, ['class'=>'birthday-field date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
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
              {{ Form::text('passport_date', $user->passport_date, ['class'=>'passport-date-field date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
              <label>Кем выдан
              {{ Form::text('passport_released', $user->passport_released, ['class'=>'passport-released-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Адрес прописки
              {{ Form::text('passport_address', $user->passport_address, ['class'=>'passport-address-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
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
              {{ Form::text('company_name', $user->company_name, ['class'=>'company-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>ИНН
              {{ Form::text('inn', $user->inn, ['class'=>'inn-field', 'maxlength'=>'12', 'pattern'=>'[0-9]{12}', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>КПП
              {{ Form::text('kpp', $user->kpp, ['class'=>'kpp-field', 'maxlength'=>'9', 'pattern'=>'[0-9]{9}', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-12 cell">
              <label>Банк
              {{ Form::text('bank', $user->bank, ['class'=>'bank-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Р/С
              {{ Form::text('account_settlement', $user->account_settlement, ['class'=>'account-settlement-field', 'maxlength'=>'20', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>К/С
              {{ Form::text('account_correspondent', $user->account_correspondent, ['class'=>'account-correspondent-field', 'maxlength'=>'20', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
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
              {{ Form::select('user_type', [ '1' => 'Сотрудник', '2' => 'Клиент']) }}
            </label>
          </div>
        </div>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell tabs-margin-top">
            <label>Логин
              {{ Form::text('login', $user->login, ['class'=>'login-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'required']) }}
              <span class="form-error">Обязательно нужно логиниться!</span>
            </label>
            <label>Пароль
              {{ Form::password('password', ['class' => 'password', 'maxlength' => '30', 'id' => 'password']) }}
              <span class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
            </label>
            <label>Пароль повторно
              {{ Form::password('password', ['class' => 'password', 'maxlength' => '30', 'id' => 'password', 'data-equalto' => 'password']) }}
              <span class="form-error">Пароли не совпадают!</span>
            </label>
          </div>
        </div>
        <div class="grid-x grid-padding-x">
          <div class="small-12 cell tabs-margin-top">
            <table>
              <caption>Уровень доступа</caption>
              <thead>
                <tr>
                  <th>Роль</th>
                  <th>Филиал</th>
                  <th>Должность</th>
                  <th>Инфа</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach ($role_users as $role_user)
                  <tr>
                    <td>
                      {{ $role_user->role->role_name }}
                    </td>
                    <td>
                      {{ $role_user->department->department_name }}
                    </td>
                    <td>
                      @if (isset($role_user->position->position_name))
                      {{ $role_user->position->position_name }}
                      @else
                      спецдолжность
                      @endif
                    </td>
                    <td>Инфа</td>
                    <td>Удаление</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
           
          </div>
          <div class="small-8 samll-offset-2 medium-6 medium-offset-3 cell">
            <a class="button" data-open="role-add">настройка доступа</a>
          </div>

          <div class="small-12 cell checkbox">
              {{ Form::checkbox('access_block', 1, $user->access_block == 1, ['id'=>'access-block-checkbox']) }}
            <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
          </div>
        </div>
      </fieldset> 
    </div>
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

