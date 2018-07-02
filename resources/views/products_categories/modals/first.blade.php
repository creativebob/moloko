<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">

    <label>Название категории
      @include('includes.inputs.name', ['value'=>$products_category->name, 'name'=>'name', 'required'=>'required'])
      <div class="item-error">Такая категория уже существует!</div>
    </label>

    <label>Тип
      {{ Form::select('products_mode_id', $products_modes_list) }}
    </label>

    <label>Мера
      {{ Form::select('units_category_id', $units_categories_list, null, ['placeholder' => 'Выберите категорию', 'id' => 'units-categories-list', 'required']) }}
    </label>

    <label>Единица измерения
      <select name="unit_id" id="units-list" required disabled></select>
    </label>

  </div>
</div>

{{ Form::hidden('type', $type) }}
{{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
{{ Form::hidden('products_category_id', $products_category->id, ['id' => 'products-category-id']) }}

<div class="grid-x align-center">

  <div class="small-8 cell checkbox">
    {{ Form::checkbox('status', 'set', null, ['id' => 'set-status']) }}
    <label for="set-status"><span>Набор</span></label>
  </div>

  {{-- Чекбокс отображения на сайте --}}
  @can ('publisher', $products_category)
  <div class="small-8 cell checkbox">
    {{ Form::checkbox('display', 1, $products_category->display, ['id' => 'display']) }}
    <label for="display"><span>Отображать на сайте</span></label>
  </div>
  @endcan

  @if ($products_category->moderation == 1)
  <div class="small-8 cell checkbox">
    {{ Form::checkbox('moderation', 1, $products_category->moderation, ['id' => 'moderation']) }}
    <label for="moderation"><span>Временная запись.</span></label>
  </div>
  @endif

  @can('god', App\ProductsCategory::class)
  <div class="small-8 cell checkbox">
    {{ Form::checkbox('system_item', 1, $products_category->system_item, ['id' => 'system-item']) }}
    <label for="system-item"><span>Системная запись.</span></label>
  </div>
  @endcan
</div>

<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>

@include('products_categories.scripts')



