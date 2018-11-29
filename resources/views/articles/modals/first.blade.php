{{ Form::open(['id'=>'form-product-add', 'data-abide', 'novalidate']) }}
        <div class="grid-x grid-padding-x align-center modal-content inputs">
          <div class="small-10 cell">
            <label>Категория товара
              <select name="products_category_id" id="products-categories-list" required>
                @php
                echo $products_categories_list;
                @endphp
              </select>
            </label>

            <label>Название товара
              @include('includes.inputs.name', ['value'=>null, 'name'=>'name', 'required' => true])
              <div class="item-error">Такой товар уже существует!</div>
            </label>

            <div class="grid-x grid-margin-x">
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
            </div>

            {{ Form::hidden('type', $type) }}
            {{ Form::hidden('entity', 'products_categories') }}

            {{-- Чекбокс отображения на сайте --}}
            @can ('publisher', App\Product::class)
            <div class="small-12 cell checkbox">
              {{ Form::checkbox('display', 1, null, ['id' => 'display-position']) }}
              <label for="display-position"><span>Отображать на сайте</span></label>
            </div>
            @endcan

            @can('god', App\Product::class)
            <div class="checkbox">
              {{ Form::checkbox('system_item', 1, null, ['id' => 'system-item-position']) }}
              <label for="system-item-position"><span>Системная запись.</span></label>
            </div>
            @endcan

          </div>
        </div>
        <div class="grid-x align-center">
          <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить товар', ['data-close', 'class'=>'button modal-button submit-product-add']) }}
          </div>
        </div>
        {{ Form::close() }}