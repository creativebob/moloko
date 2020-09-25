<div class="cell small-12">
    <label>Логин
        {{ Form::text('login', $user->login, ['class' => 'login-field', 'maxlength' => '30', 'autocomplete' => 'new-login', 'pattern' => '[A-Za-z0-9._-]{6,30}']) }}
        <span class="form-error">Обязательно нужно логиниться!</span>
    </label>
    <label>Пароль
        {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password', 'pattern' => '[A-Za-z0-9]{6,20}', 'autocomplete' => 'new-password']) }}
        <span
            class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
    </label>
    <label>Пароль повторно
        {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password-repeat', 'data-equalto' => 'password', 'pattern' => '[A-Za-z0-9]{6,20}', 'autocomplete' => 'new-password']) }}
        <span class="form-error">Пароли не совпадают!</span>
    </label>
</div>

<div class="cell small-12 text-center checkbox">
    {!! Form::hidden('access_block', 0) !!}
    {{ Form::checkbox('access_block', 1, $user->access_block == 1, ['id'=>'access-block-checkbox']) }}
    <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
</div>
