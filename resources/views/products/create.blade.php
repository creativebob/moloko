<div class="reveal" id="first-add" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ продукции</h5>
    </div>
  </div>
  {{ Form::open(['url' => 'products', 'id'=>'form-first-add', 'data-abide', 'novalidate']) }}

  <div class="grid-x grid-padding-x align-center modal-content inputs">
    <div class="small-10 cell">
      <label>Название товара
        @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required'=>'required'])
        <div class="item-error">Такой товар уже существует!</div>
      </label>

      <label>Категория товара
        <select name="products_category_id">
          @php
          echo $products_categories_list;
          @endphp
        </select>
      </label>

      {{-- Чекбокс системной записи --}}
      @can ('god', App\Product::class)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>null, 'name'=>'system_item']) 
      </div>
      @endcan

    </div>
  </div>
  <div class="grid-x align-center">
    <div class="small-6 medium-4 cell">
      {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button']) }}
    </div>
  </div>

  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>

@include('includes.scripts.inputs-mask')





