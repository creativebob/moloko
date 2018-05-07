<div class="grid-x grid-padding-x inputs">

  <div class="small-12 medium-7 large-5 cell tabs-margin-top">

    @if ($errors->any())
    <div class="alert callout" data-closable>
      <h5>Неправильный формат данных:</h5>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    @endif
    
    <div class="grid-x grid-padding-x">
      <div class="small-12 medium-6 cell">
        <label>Название товара
          @include('includes.inputs.name', ['value'=>$product->name, 'name'=>'name', 'required'=>'required'])
        </label>
        <label>Артикул товара
          @include('includes.inputs.name', ['value'=>$product->article, 'name'=>'alricle', 'required'=>'required'])
        </label>
      </div>
      <div class="small-12 medium-6 cell">
        <label>Категория товара
          <select name="products_category_id">
            @php
            echo $products_categories_list;
            @endphp
          </select>
        </label>
        <label>Себестоимость товара (руб.)
          @include('includes.inputs.name', ['value'=>$product->cost, 'name'=>'cost', 'required'=>'required'])
        </label>
      </div>
      <div class="small-12 medium-6 cell">
        <label>Единица измерения
          {{ Form::select('unit_id', $units_list, $product->unit_id)}}
        </label>
      </div>
        <div class="small-12 medium-6 cell">
        <label>Страна производитель
          {{ Form::select('country_id', $countries_list, $product->country_id)}}
        </label>
      </div>
      <div class="small-12 cell">
        <label>Описание товара
          @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$product->description, 'required'=>''])
        </label>
      </div>
    </div>
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">
  </div>

  {{-- Чекбокс модерации --}}
  @can ('moderator', $product)
  @if ($product->moderation == 1)
  <div class="small-12 cell checkbox">
    @include('includes.inputs.moderation', ['value'=>$product->moderation, 'name'=>'moderation'])
  </div>
  @endif
  @endcan

  {{-- Чекбокс системной записи --}}
  @can ('god', $product)
  <div class="small-12 cell checkbox">
    @include('includes.inputs.system', ['value'=>$product->system_item, 'name'=>'system_item']) 
  </div>
  @endcan   

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>
