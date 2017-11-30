@extends('layouts.app')

@section('inhead')
	<link rel="stylesheet" href="js/pickmeup/css/pickmeup.css">
	<script type="text/javascript" src="js/pickmeup/js/jquery.js"></script>
	<script type="text/javascript" src="js/pickmeup/js/jquery.pickmeup.js"></script>
	<script type="text/javascript" src="js/pickmeup/js/demo.js"></script>
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
<form action="/user" method="POST" data-abide novalidate>
  {{ csrf_field() }}
  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
      <div class="tabs-content" data-tabs-content="tabs">
        <!-- Учетные данные -->
        <div class="tabs-panel is-active" id="content-panel-1">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Короткое имя
                <input type="text" name="nickname" autocomplete="off">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Телефон
                <input class="phone-field" type="text" name="phone" maxlength="17" class="phone_field" pattern="8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" autocomplete="off" required>
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Телефон
                <input class="phone-field" type="text" name="extra_phone" maxlength="17" class="phone_field" pattern="8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}" autocomplete="off" required>
                <span class="form-error">Введите все символы телефонного номера!</span>
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Почта
                <input type="text" name="email" autocomplete="off">
              </label>
              <label>Телеграм ID
                <input type="text" name="telegram_id" autocomplete="off">
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label class="input-icon">Город
                <input type="text" name="id_city" autocomplete="off">
                <div class="sprite-input icon-password"></div>
              </label>
              <label>Адрес
                <input type="text" name="address" autocomplete="off">
              </label>
            </div>
          </div>
        </div>
        <!-- Персональные данные -->
        <div class="tabs-panel" id="content-panel-2">
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Фамилия
                <input type="text" name="first_name" autocomplete="off">
              </label>
              <label>Имя
                <input type="text" name="second_name" autocomplete="off">
              </label>
              <label>Отчество
                <input type="text" name="patronymic" autocomplete="off">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-5 medium-4 cell">
              <label>Дата рождения
                <input type="text" name="birthday" class="date-field" pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}">
              </label>
            </div>
            <div class="small-6 small-offset-1 medium-6 medium-offset-2 cell radiobutton">Пол<br>
              <input type="radio" name="sex" id="man" value="1" checked>
              <label for="man"><span>Мужской</span></label>
              <input type="radio" name="sex" id="woman" value="0">
              <label for="woman"><span>Женский</span></label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Паспорт (серия, номер)
                <input class="passport-field" type="text" name="passport_number" maxlength="13" pattern="[0-9]{2} [0-9]{2} №[0-9]{6}" autocomplete="off">
              </label>
            </div>
            <div class="small-5 medium-6 cell">
              <label>Когда выдан
                <input type="text" name="passport_date" class="date-field" pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}" autocomplete="off">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
              <label>Кем выдан
                <input type="text" name="passport_released" autocomplete="off">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
              <label>Адрес
                <input type="text" name="passport_address" autocomplete="off">
              </label>
            </div>
          </div>
        </div>
        <!-- Представитель компании -->
        <div class="tabs-panel" id="content-panel-3">
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 cell checkbox">
              <input type="checkbox" name="orgform_status" id="company-checkbox" value="1">
              <label for="company-checkbox"><span>Представитель компании</span></label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Название компании
                <input type="text" name="company_name">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>ИНН
                <input class="inn-field" type="text" name="inn" maxlength="12" pattern="[0-9]{12}" autocomplete="off">
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>КПП
                <input class="kpp-field" type="text" name="kpp" maxlength="9" pattern="[0-9]{9}" autocomplete="off">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-12 cell">
              <label>Банк
                <input type="text" name="bank" autocomplete="off">
              </label>
            </div>
          </div>
          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Р/С
                <input class="account-field" type="text" name="account_settlement" maxlength="20" pattern="[0-9]{20}" autocomplete="off">
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>К/С
                <input class="account-field" type="text" name="account_correspondent" maxlength="20" pattern="[0-9]{20}" autocomplete="off">
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
              <select name="contragent_status">
                <option value="0">Клиент</option>
                <option value="1">Сотрудник</option>
              </select>
            </label>
          </div>
        </div>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell tabs-margin-top">
            <label>Логин
              <input type="text" name="login" required>
              <span class="form-error">Обязательно нужно логиниться!</span>
            </label>
            <label>Пароль
              <input type="password" class="password" maxlength="30" id="password" name="password" required>
              <span class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
            </label>
            <label>Пароль повторно
              <input type="password" class="password" maxlength="30" data-equalto="password" required>
              <span class="form-error">Пароли не совпадают!</span>
            </label>
          </div>
        </div>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell tabs-margin-top">
            <label>Уровень доступа
              <select name="group_users_id">
                <option>Лол1</option>
                <option>Лол2</option>
                <option>Лол3</option>
                <option>Лол4</option>
                <option>Лол5</option>
                <option>Лол6</option>
              </select>
            </label>
          </div>
          <div class="small-12 cell">
            <label>Область доступа
              <select name="group_filials_id">
                <option>Лол</option>
              </select>
            </label>
          </div>
          <div class="small-12 cell checkbox">
            <input type="checkbox" name="access_block" id="access" value="1">
            <label for="access"><span>Блокировать доступ</span></label>
          </div>
        </div>
      </fieldset> 
    </div>
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      <input type="submit" class="button" value="Сохранить">
    </div>
  </div>
</form>
@endsection

@section('scripts')
<script type="text/javascript" src="js/jquery.inputmask.min.js"></script>
<script type="text/javascript">

  $(function() {
    // Определяем маски для полей
    $('.passport-field').mask('00 00 №000000');
    $('.phone-field').mask('8 (000) 000-00-00');
    $('.inn-field').mask('000000000000');
    $('.kpp-field').mask('000000000');
    $('.account-field').mask('00000000000000000000');
    $('.date-field').mask('00.00.0000');


  });

  // Прикручиваем календарь
  $('.date-field').pickmeup({
    position : "bottom",
    hide_on_select : true
  });
  
</script>
@endsection


