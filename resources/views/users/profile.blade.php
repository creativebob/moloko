@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать профиль')

{{-- @section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $user->second_name.' '.$user->first_name)) --}}

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
   <h2 class="header-content">МОЙ ПРОФИЛЬ</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')

{{ Form::model($user, ['route' => ['users.update_profile'], 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files'=>'true']) }}
{{ method_field('PATCH') }}

<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Основные данные</a></li>
      <li class="tabs-title"><a data-tabs-target="content-panel-2" href="#content-panel-2">Персональные данные</a></li>
      {{-- <li class="tabs-title"><a data-tabs-target="content-panel-3" href="#content-panel-3">Представитель компании</a></li> --}}
      <li class="tabs-title"><a data-tabs-target="content-panel-4" href="#content-panel-4">Образование и опыт</a></li>
      <li class="tabs-title"><a data-tabs-target="settings" href="#settings">Настройки</a></li>
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
              <photo-upload-component :photo='@json($user->photo)'></photo-upload-component>
          </div>
        </div>

        <div class="grid-x grid-padding-x tabs-margin-top">
          <div class="small-12 medium-6 cell">
            <label>Телефон
              @include('includes.inputs.phone', ['value' => isset($user->main_phone->phone) ? $user->main_phone->phone : null, 'name'=>'main_phone', 'required' => true])
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
            <label>Страна
              @php
              $country_id = null;
              if (isset($user->location->country_id)) {
              $country_id = $user->location->country_id;
            }
            @endphp
            {{ Form::select('country_id', $countries_list, $country_id)}}
          </label>
        </div>
        <div class="small-12 medium-6 cell">
            @include('system.common.includes.city_search', ['item' => $user, 'required' => true])
      </div>


      <div class="small-12 medium-6 cell">
        <label>Адрес
          @php
          $address = null;
          if (isset($user->location->address)) {
          $address = $user->location->address;
        }
        @endphp
        @include('includes.inputs.address', ['value'=>$address, 'name'=>'address'])
      </label>
    </div>

    <div class="small-12 medium-6 cell">
      <label>Почта
        @include('includes.inputs.email', ['value'=>$user->email, 'name'=>'email', 'required' => true])
      </label>
    </div>

    <div class="small-12 medium-6 cell">
      <label>Телеграм ID
        {{ Form::text('telegram', $user->telegram, ['class'=>'telegram-id-field', 'pattern'=>'[0-9]{9,12}', 'maxlength'=>'12', 'autocomplete'=>'off']) }}
        <span class="form-error">Укажите номер Telegram</span>
      </label>
    </div>
  </div>

</div>


<!-- Персональные данные -->
<div class="tabs-panel" id="content-panel-2">
  <div class="grid-x grid-padding-x">
    <div class="small-5 medium-4 cell">
      <label>Дата рождения
        @include('includes.inputs.date', ['name'=>'birthday_date', 'value'=>$user->birthday_date])
      </label>
    </div>
    <div class="small-6 small-offset-1 medium-6 medium-offset-2 cell radiobutton">Пол<br>

      {{ Form::radio('sex', 1, true, ['id'=>'man']) }}
      <label for="man"><span>Мужской</span></label>

      {{ Form::radio('sex', 0, false, ['id'=>'woman']) }}
      <label for="woman"><span>Женский</span></label>

    </div>
  </div>

  {{--
    <div class="grid-x grid-padding-x">
      <div class="small-12 medium-6 cell">
        <label>Паспорт (серия, номер)
          {{ Form::text('passport_number', $user->passport_number, ['class'=>'passport-number-field', 'pattern'=>'[0-9]{2} [0-9]{2} №[0-9]{6}', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
        </label>
      </div>
      <div class="small-5 medium-6 cell">
        <label>Когда выдан
          @include('includes.inputs.date', ['name'=>'passport_date', 'value'=>$user->passport_date])
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
    --}}



  </div>


  {{--
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

    <!-- Образование и опыт -->
    <div class="tabs-panel" id="content-panel-4">
      <div class="grid-x grid-padding-x">
        <div class="small-12 medium-6 cell">
          <label>Специальность
            @include('includes.inputs.string', ['name'=>'specialty', 'value'=>$user->specialty])
          </label>
        </div>
        <div class="small-12 medium-6 cell">
          <label>Ученая степень, звание
            @include('includes.inputs.string', ['name'=>'degree', 'value'=>$user->degree])
          </label>
        </div>
      </div>
      <div class="grid-x grid-padding-x">
        <div class="small-12 medium-12 cell">
          <label>Информация о человеке (Для сайта):
            {{ Form::textarea('about', $user->about, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
          </label><br>
        </div>
      </div>

      <div class="grid-x grid-padding-x">
        <div class="small-12 medium-12 cell">
          <label>Фраза
            @include('includes.inputs.string', ['name'=>'quote', 'value'=>$user->quote])
          </label>
        </div>

      </div>


    </div>

    <!-- Оповещения -->
    <div class="tabs-panel" id="settings">
      <fieldset class="fieldset-access">
        <legend>Настройка оповещений</legend>
        <div class="grid-x grid-padding-x">
          <div class="small-12 cell">

            <ul>
              @foreach ($user->staff->first()->position->notifications as $notification)
              <li>
                <div class="small-12 cell checkbox">
                  {{ Form::checkbox('notifications[]', $notification->id, null, ['id'=>'notification-'.$notification->id, 'class'=>'access-checkbox']) }}
                  <label for="notification-{{ $notification->id }}"><span>{{ $notification->name }}</span></label>
                </div>
              </li>
              @endforeach
            </ul>

          </div>
        </div>
      </fieldset>
    </div>

  </div>
</div>
<div class="small-12 medium-5 medium-offset-1 large-5 large-offset-2 cell">
  <fieldset class="fieldset-access">
    <legend>Настройка доступа</legend>
    <div class="grid-x grid-padding-x">
      <div class="small-12 cell tabs-margin-top">

        <input type="hidden" value='1' name="user_type">
        <input type="hidden" value='true' name="users_edit_mode">

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
    </div>
  </fieldset>
</div>

<input type="hidden" name='backroute' value="companies">

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
  @include('includes.inputs.system', ['value'=>$user->system, 'name'=>'system'])
</div>
@endcan

<div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
  {{ Form::submit('Редактировать', ['class'=>'button']) }}
</div>
</div>

{{ Form::close() }}

@endsection

@section('modals')

{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
@include('users.scripts')
@include('includes.scripts.cities-list')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.delete-from-page-script')
@include('includes.scripts.upload-file')
@endsection


