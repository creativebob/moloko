

<!-- Основные -->
<div class="grid-x grid-padding-x modal-content inputs">
  <div class="small-10 small-offset-1 cell">
    <label>Расположение
      <select name="parent_id">
        @php
        echo $products_categories_list;
        @endphp
      </select>
    </label>
    <label>Название
      @include('includes.inputs.name', ['value'=>$products_category->name, 'name'=>'name', 'required'=>'required'])
      <div class="sprite-input-right find-status"></div>
      <div class="item-error">Такой уже существует!</div>
    </label>

    <label>Мера
      {{ Form::select('units_category_id', $units_categories_list, null, ['placeholder' => 'Выберите категорию', 'id' => 'units-categories-list', 'required']) }}
    </label>

    <label>Единица измерения
      <select name="unit_id" id="units-list" required disabled></select>
    </label>

  </div>
</div>

{{ Form::hidden('products_category_id', $products_category->id, ['id' => 'products-category-id']) }}
{{ Form::hidden('medium_item', 1, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
{{ Form::hidden('category_id', 0, ['class' => 'category-id']) }}
{{ Form::hidden('type', $type) }}

<div class="grid-x align-center">

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
  <div class="small-6 medium-6 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>


@include('products_categories.scripts')
