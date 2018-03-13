{{ Form::open(['route' => 'users.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

{{-- Подключаем класс Checkboxer --}}
@include('includes.scripts.class.checkboxer')

  <legend>Фильтрация</legend>
  <div class="grid-x grid-padding-x"> 
    
    <div class="small-12 medium-4 large-2 cell checkbox checkboxer">
      @include('includes.inputs.checkboxer', ['name'=>'city'])
    </div>


    <div class="small-12 medium-12 align-center cell tabs-button">
      {{ Form::submit('Фильтрация', ['class'=>'button']) }}
    </div>

  </div>
{{ Form::close() }}