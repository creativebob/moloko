<div class="up-input-button">
<a id="mode-default" class="modes">Вернуться</a>
</div>
<label>Введите название новой группы
  @include('includes.inputs.string', ['value'=>null, 'name'=>'service_product_name', 'required'=>'required'])
</label>
<label>Название услуги
  @include('includes.inputs.string', ['value'=>null, 'name'=>'name', 'required'=>'required'])
  <div class="item-error">Названия услуги и группы услуг не должны совпадать!</div>
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
{{ Form::hidden('mode', 'mode-add') }}