@extends('layouts.app')

@section('inhead')
	<link rel="stylesheet" href="/js/pickmeup/css/pickmeup.css">
	<script type="text/javascript" src="/js/pickmeup/js/jquery.js"></script>
	<script type="text/javascript" src="/js/pickmeup/js/jquery.pickmeup.js"></script>
	<script type="text/javascript" src="/js/pickmeup/js/demo.js"></script>
@endsection

@section('title', 'Новый пользователь')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">НОВЫЙ ПОЛЬЗОВАТЕЛЬ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')
<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Учетные данные</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Персональные данные</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">Представитель компании</a></li>
    </ul>
  </div>
</div>

{{ Form::model($users, ['route' => ['users.update', $users->id], 'data-abide', 'novalidate']) }}
<!-- <form action="/user" method="POST" data-abide novalidate> -->
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
      <div class="tabs-content" data-tabs-content="tabs">
        <!-- Учетные данные -->
        <div class="tabs-panel is-active" id="content-panel-1">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Короткое имя
                {{ Form::text('nickname', $users->nickname, ['class'=>'nickname-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'required']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Телефон
                {{ Form::text('phone', $users->phone, ['class'=>'phone-field', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off', 'required']) }}
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Телефон
                {{ Form::text('extra_phone', $users->extra_phone, ['class'=>'phone-field', 'pattern'=>'8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}', 'maxlength'=>'17', 'autocomplete'=>'off']) }}
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Почта
                {{ Form::text('email', $users->email, ['class'=>'email-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите почту</span>
              </label>
              <label>Телеграм ID
                {{ Form::text('telegram_id', $users->telegram_id, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
                <span class="form-error">Укажите номер Telegram</span>
              </label>
              
            </div>
            <div class="small-12 medium-6 cell">
              <label class="input-icon">Город
                <input type="text" name="id_city" autocomplete="off">
                <div class="sprite-input icon-password"></div>
              </label>
              <label>Адрес
              {{ Form::text('address', $users->address, ['class'=>'address-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
        </div>
        <!-- Персональные данные -->
        <div class="tabs-panel" id="content-panel-2">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Имя
              {{ Form::text('second_name', $users->second_name, ['class'=>'second-name-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
              <label>Фамилия
              {{ Form::text('first_name', $users->first_name, ['class'=>'first-name-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
              <label>Отчество
              {{ Form::text('patronymic', $users->patronymic, ['class'=>'patronymic-field', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-5 medium-4 cell">
              <label>Дата рождения
              {{ Form::text('birthday', $users->birthday, ['class'=>'birthday-field date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
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
              {{ Form::text('passport_number', $users->passport_number, ['class'=>'passport-number-field', 'pattern'=>'[0-9]{2} [0-9]{2} №[0-9]{6}', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-5 medium-6 cell">
              <label>Когда выдан
              {{ Form::text('passport_date', $users->passport_date, ['class'=>'passport-date-field date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
              <label>Кем выдан
              {{ Form::text('passport_released', $users->passport_released, ['class'=>'passport-released-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Адрес прописки
              {{ Form::text('passport_address', $users->passport_address, ['class'=>'passport-address-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
        </div>
        <!-- Представитель компании -->
        <div class="tabs-panel" id="content-panel-3">
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 cell checkbox">
              {{ Form::checkbox('orgform_status', 0, false, ['id'=>'orgform-status-checkbox']) }}
              <label for="orgform-status-checkbox"><span>Представитель компании</span></label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Название компании
              {{ Form::text('company_name', $users->company_name, ['class'=>'company-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>ИНН
              {{ Form::text('inn', $users->inn, ['class'=>'inn-field', 'maxlength'=>'12', 'pattern'=>'[0-9]{12}', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>КПП
              {{ Form::text('kpp', $users->kpp, ['class'=>'kpp-field', 'maxlength'=>'9', 'pattern'=>'[0-9]{9}', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-12 cell">
              <label>Банк
              {{ Form::text('bank', $users->bank, ['class'=>'bank-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Р/С
              {{ Form::text('account_settlement', $users->account_settlement, ['class'=>'account-settlement-field', 'maxlength'=>'20', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>К/С
              {{ Form::text('account_correspondent', $users->account_correspondent, ['class'=>'account-correspondent-field', 'maxlength'=>'30', 'pattern'=>'[0-9]{20}', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="small-12 medium-4 medium-offset-1 large-4 large-offset-3 cell">
      <fieldset class="fieldset-access">
        <legend>Настройка доступа</legend>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell">
            <label>Статус пользователя
              {{ Form::select('contragent_status', ['1' => 'Клиент', '2' => 'Сотрудник']) }}
            </label>
          </div>
        </div>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell tabs-margin-top">
            <label>Логин
              {{ Form::text('login', $users->login, ['class'=>'login-field', 'maxlength'=>'20', 'autocomplete'=>'off', 'required']) }}
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
            <label>Уровень доступа
              {{ Form::select('group_users_id', ['1' => 'Менеджер', '2' => 'Администратор']) }}
            </label>
          </div>
          <div class="small-12 cell">
            <label>Область доступа
              {{ Form::select('group_filials_id', ['1' => 'Иркутск', '2' => 'Улан-Удэ']) }}
            </label>
          </div>
          <div class="small-12 cell checkbox">
              {{ Form::checkbox('access_block', 0, false, ['id'=>'access-block-checkbox']) }}
            <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
          </div>
        </div>
      </fieldset> 
    </div>
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit('Сохранить', ['class'=>'button']) }}
    </div>
  </div>

{{ Form::close() }}

@endsection

@section('scripts')
<script type="text/javascript" src="/js/jquery.inputmask.min.js"></script>
<script type="text/javascript">

  $(function() {
    // Определяем маски для полей
    $('.passport-number-field').mask('00 00 №000000');
    $('.phone-field').mask('8 (000) 000-00-00');
    $('.inn-field').mask('000000000000');
    $('.kpp-field').mask('000000000');
    $('.account-correspondent-field').mask('00000000000000000000');
    $('.account-settlement-field').mask('00000000000000000000');
    $('.birthday-field').mask('00.00.0000');
    $('.passport-date-field').mask('00.00.0000');

  });

  // Прикручиваем календарь
  $('.date-field').pickmeup({
    position : "bottom",
    hide_on_select : true
  });
  
</script>
@endsection


