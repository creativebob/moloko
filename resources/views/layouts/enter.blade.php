<!doctype html>
<html class="no-js" lang="ru" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="crm/css/foundation.css">
    <link rel="stylesheet" href="crm/css/app.css">
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    </style>
    <title>CRM System ВК "МАРС"</title>
  </head>
  <body class="enter-page">
    <div class="grid-x">
      <div class="small-10 cell enter">
        <img class="logo" src="/crm/img/logo-creativebob.svg">
        <form action="{{ route('login') }}" method="POST" data-abide novalidate>
          {{ csrf_field() }}
          <label class="input-icon">
            <input type="text" name="login" placeholder="Логин" maxlength="25" autocomplete="off" required>
            <div class="sprite-input-left icon-login"></div>
            <span class="form-error">Обязательно нужно логиниться!</span>
          </label>
          <label class="input-icon">
            <input type="password" name="password" placeholder="Пароль" maxlength="25" autocomplete="off" required>
            <div class="sprite-input-left icon-password"></div>
            <span class="form-error">И пароль не помешает вовсе!</span>
          </label>
          <button class="button" type="submit" value="Submit">Войти</button>
          <div class="checkbox">
            <input type="checkbox" name="" id="remember">
            <label for="remember" class="remember"><span>Запомнить меня</span></label>
          </div>
        </form>
        <a href="" class="forgot">Кажется пароль забыл...</a>
      </div>
    </div>
    <script src="/crm/js/vendor/what-input.js"></script>
    <script src="/crm/js/vendor/foundation.js"></script>
    <script src="/crm/js/app.js"></script>
  </body>
</html>
   