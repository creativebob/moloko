
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
            <img id="photo" @if (isset($products_category->photo_id)) src="/storage/{{ $products_category->company->id }}/media/products/{{ $products_category->id }}/img/medium/{{ $products_category->photo->name }}" @endif>
          </div>
        </div>
      </div>
    </div>

    {{ Form::hidden('products_category_id', $products_category->id, ['id' => 'products-category-id']) }}
    {{ Form::hidden('medium_item', 1, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
    {{ Form::hidden('category_id', 0, ['class' => 'category-id']) }}

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
  </div>
</div> 