{{ Form::open(['route' => 'employees.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}
  <legend>Фильтрация</legend>
  <div class="grid-x grid-padding-x">

    <div class="small-5 medium-4 large-2 cell">
      <label>Начальная дата
        @include('includes.inputs.date', ['name'=>'date_start', 'value'=>'', 'required'=>''])
      </label>
    </div>

    <div class="small-5 medium-4 large-2 cell">
      <label>Конечная дата
        @include('includes.inputs.date', ['name'=>'date_end', 'value'=>'', 'required'=>''])
      </label>
    </div>

    <div class="small-12 medium-12 align-center cell tabs-button">
      {{ Form::submit('Применить', ['class'=>'button']) }}
      <a href="/employees" class="button">Сбросить</a>
    </div>

  </div>
{{ Form::close() }}