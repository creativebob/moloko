{{ Form::open(['url' => 'sites.$site->site_alias.pages.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}
  <legend>Фильтрация</legend>
  <div class="grid-x grid-padding-x"> 
    <div class="small-6 cell">
      <label>Статус пользователя
        {{ Form::select('user_type', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
      </label>
    </div>
    <div class="small-6 cell">
      <label>Блокировка доступа
        {{ Form::select('access_block', [ 'all' => 'Все пользователи', '1' => 'Доступ блокирован', '' => 'Доступ открыт'], 'all') }}
      </label>
    </div>

    <div class="small-12 medium-12 align-center cell tabs-button">
      {{ Form::submit('Фильтрация', ['class'=>'button']) }}
    </div>
  </div>
{{ Form::close() }}