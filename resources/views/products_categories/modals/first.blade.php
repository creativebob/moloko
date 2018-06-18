
<div class="grid-x tabs-wrap align-center tabs-margin-top">
  <div class="small-8 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#basic" aria-selected="true">Основные</a></li>
      <li class="tabs-title"><a data-tabs-target="settings" href="#settings">Дополнительные</a></li>
    </ul>
  </div>
</div>
<div class="tabs-wrap inputs">
  <div class="tabs-content" data-tabs-content="tabs">

    <!-- Основные -->
    <div class="tabs-panel is-active" id="basic">

      <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10 cell">

          <label>Название категории
            @include('includes.inputs.name', ['value'=>$products_category->name, 'name'=>'name', 'required'=>'required'])
            <div class="item-error">Такая категория уже существует!</div>
          </label>
          <label>Тип продукции
            @can('god', App\ProductsCategory::class)
            @php
            $disabled = '';
            @endphp
            @endcan
            {{ Form::select('products_type_id', $products_types_list, $products_category->products_type_id, [$disabled])}}
          </label>

        </div>
      </div>
    </div>

    <!-- Дополнительные -->
    <div class="tabs-panel" id="settings">

      <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10 cell">

          <label>Описание
            @include('includes.inputs.textarea', ['value'=>$products_category->description, 'name'=>'description', 'required'=>''])
          </label>
          <label>Описание для сайта
            @include('includes.inputs.textarea', ['value'=>$products_category->seo_description, 'name'=>'seo_description', 'required'=>''])
          </label>
          <label>Выберите аватар
            {{ Form::file('photo') }}
          </label>
          <div class="text-center">
            <img id="photo" @if (isset($products_category->photo_id)) src="/storage/{{ $products_category->company_id }}/media/products_categories/{{ $products_category->id }}/img/medium/{{ $products_category->photo->name }}" @endif>
          </div>
        </div>
      </div>
    </div>

    {{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
    {{ Form::hidden('products_category_id', $products_category->id, ['id' => 'products-category-id']) }}

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
      <div class="small-6 medium-4 cell">
        {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
      </div>
    </div>

  </div>
</div>
