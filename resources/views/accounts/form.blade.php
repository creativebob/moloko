

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-12 large-6 cell tabs-margin-top">
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

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-12 cell">
                    <div class="grid-x grid-padding-x">

                        <div class="small-12 cell">

                            @include('includes.selects.source_with_source_services', ['source_service' => $account->source_service])

                        </div>

                        <div class="small-12 medium-12 cell">

                            <label>Имя
                                @include('includes.inputs.name', ['value'=>$account->name])
                            </label>

                            <label>Описание
                              @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$account->description])
                          </label>

                      </div>


                    <div class="small-12 medium-6 cell">
                      <label>Идентификатор (ID)
                          {{ Form::text('external_id', $account->external_id) }}
                    </label>

                        <label>Алиас
                            @include('includes.inputs.alias', ['name'=>'alias', 'value'=>$account->alias])
                        </label>
                </div>


            </div>
        </div>
        <div class="small-12 medium-12 cell">
            <div class="grid-x grid-padding-x">

              <div class="small-12 medium-6 cell">
                <label>Логин
                  {{ Form::text('login', $account->login, ['class'=>'login-field', 'maxlength'=>'30', 'autocomplete'=>'off', 'required', 'pattern'=>'[A-Za-z0-9._-]{6,30}']) }}
                  <span class="form-error">Требуется логин!</span>
              </label>
              <label ondblclick="alert('{{ $account->password }}');">Пароль
                  {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '20', 'id' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
                  <span class="form-error">Введите пароль и повторите его, ну а что поделать, меняем ведь данные!</span>
              </label>
              <label>Пароль повторно
                  {{ Form::password('password', ['class' => 'password password-field', 'maxlength' => '30', 'id' => 'password-repeat', 'data-equalto' => 'password', 'pattern'=>'[A-Za-z0-9]{6,20}', 'autocomplete'=>'off']) }}
                  <span class="form-error">Пароли не совпадают!</span>
              </label>
          </div>

          <div class="small-12 medium-6 cell">
            <label>API токен
                @include('includes.inputs.string', ['name'=>'api_token', 'value'=>$account->api_token])
            </label>
            <label>Секрет
                @include('includes.inputs.text-en', ['name'=>'secret', 'value'=>$account->secret])
            </label>

              <label>Публичная страница (ссылка)
                  {{ Form::text('page_public_url', $account->page_public_url) }}
              </label>
        </div>
    </div>
</div>



</div>

</div>
</div>

<div class="small-12 medium-6 large-6 cell tabs-margin-top">
</div>

{{-- Чекбоксы управления --}}
@include('includes.control.checkboxes', ['item' => $account])

<div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
</div>
</div>

