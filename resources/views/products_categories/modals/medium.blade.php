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
    <label>Тип продукции
      {{ Form::select('products_type_id', $products_types_list, $products_category->products_type_id)}}
    </label>
    {{ Form::hidden('products_category_id', $products_category->id, ['id' => 'products-category-id']) }}
    {{ Form::hidden('medium_item', 0, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
    @if ($products_category->moderation == 1)
      <div class="checkbox">
        {{ Form::checkbox('moderation', 1, $products_category->moderation, ['id' => 'moderation']) }}
        <label for="moderation"><span>Временная запись.</span></label>
      </div>
      @endif
      @can('god', App\ProductsCategory::class)
      <div class="checkbox">
        {{ Form::checkbox('system_item', 1, $products_category->system_item, ['id' => 'system-item']) }}
        <label for="system-item"><span>Системная запись.</span></label>
      </div>
      @endcan
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-6 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>