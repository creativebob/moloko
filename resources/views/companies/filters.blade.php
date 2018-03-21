{{ Form::open(['route' => 'companies.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

{{-- Подключаем класс Checkboxer --}}
@include('includes.scripts.class.checkboxer')

<div class="grid-x grid-padding-x">

  <div class="small-12 medium-12 large-6 cell">
  <legend>Основные фильтры:</legend>
    <div class="grid-x">
        <div class="small-12 medium-6 large-6 cell checkbox checkboxer">
          @include('includes.inputs.checkboxer', ['name'=>'city', 'value'=>$filter])
        </div>
    </div>

    <div class="grid-x">
        <div class="small-12 medium-6 large-6 cell checkbox checkboxer">
          @include('includes.inputs.checkboxer', ['name'=>'sector', 'value'=>$filter])
        </div>
    </div>

  </div>

  <div class="small-12 medium-12 large-6 cell">
  <legend>Мои списки:</legend>
    <div class="grid-x">
      
      <div class="small-12 medium-12 large-12 cell checkbox checkboxer" id="booklister">
          @include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])
      </div>

    </div>
  </div>

  <div class="small-12 medium-12 large-3 align-center cell tabs-button">
      {{ Form::submit('Фильтрация', ['class'=>'button']) }}
  </div>

</div>

{{ Form::close() }}