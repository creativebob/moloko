<label>Название группы услуг
  @include('includes.inputs.name', ['value'=>null, 'name'=>'service_product_name', 'required'=>'required'])
  <div class="item-error">Такой товар уже существует!</div>
</label>
<label>Название услуги
  @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
  <div class="item-error">Такой товар уже существует!</div>
</label>

{{-- <div class="grid-x grid-margin-x">
  <div class="small-12 medium-6 cell">
    <label>Категория единиц измерения
      {{ Form::select('units_category_id', $units_categories_list, null, ['placeholder' => 'Выберите категорию', 'id' => 'units-categories-list', 'required']) }}
    </label>
  </div>
  <div class="small-12 medium-6 cell">
    <label>Единица измерения
      <select name="unit_id" id="units-list" required disabled></select>
    </label>
  </div>
</div> --}}
{{ Form::hidden('mode', 'mode_add') }}