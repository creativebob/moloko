{{ Form::open(['route' => 'companies.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

{{-- Подключаем класс Checkboxer --}}
@include('includes.scripts.class.checkboxer')

  <legend>Фильтрация</legend>
  <div class="grid-x grid-padding-x"> 

    <div class="small-12 medium-4 large-2 cell checkbox checkboxer">
      @include('includes.inputs.checkboxer', ['name'=>'city', 'value'=>$filter])
    </div>

    <div class="small-12 medium-4 large-2 cell checkbox checkboxer">
      @include('includes.inputs.checkboxer', ['name'=>'sector', 'value'=>$filter])
    </div>

    <div class="small-12 medium-4 large-2 cell checkbox checkboxer">
      @include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])
    </div>

    <div class="small-12 medium-12 align-center cell tabs-button filter-submit">
      {{ Form::submit('Применить', ['class'=>'button']) }}
    </div>

  </div>
{{ Form::close() }}