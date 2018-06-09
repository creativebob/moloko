@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@endsection

@section('title', 'Редактировать товар')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $product))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
    <h2 class="header-content">РЕДАКТИРОВАТЬ товар &laquo{{ $product->name }}&raquo</h2>
  </div>
  <div class="top-bar-right">
  </div>
</div>
@endsection

@section('content')
<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#options" aria-selected="true">Общая информация</a></li>
      <li class="tabs-title"><a data-tabs-target="properties" href="#properties">Свойства</a></li>
      <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
      <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li>
      <li class="tabs-title"><a data-tabs-target="photos" href="#photos">Фотографии</a></li>
    </ul>
  </div>
</div>

<div class="grid-x tabs-wrap inputs">
  <div class="small-12 cell tabs-margin-top">
    <div class="tabs-content" data-tabs-content="tabs">

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

      {{ Form::model($product, ['route' => ['products.update', $product->id], 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'product-form']) }}
      {{ method_field('PATCH') }}

      <!-- Общая информация -->
      <div class="tabs-panel is-active" id="options">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-6 cell">
            <label>Название товара
              @include('includes.inputs.name', ['value'=>$product->name, 'name'=>'name', 'required'=>'required'])
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
          </div>
          <div class="small-12 medium-6 cell">
            <label>Категория измерения товара
              {{ Form::select('units_category_id', $units_categories_list, $product->units_category_id)}}
            </label>
          </div>
          <div class="small-12 medium-6 cell">
            <label>Производитель
              {{ Form::select('manufacturer_id', $manufacturers_list, $product->manufacturer_id, ['placeholder' => 'Выберите производителя'])}}
            </label>
          </div>
          <div class="small-12 cell">
            <label>Описание товара
              @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$product->description, 'required'=>''])
            </label>
          </div>
          <div class="small-12 cell">
            <label>Выберите аватар
              {{ Form::file('photo') }}
            </label>
            <div class="text-center">
              <img id="photo" @if (isset($product->photo_id)) src="/storage/{{ $product->company->id }}/media/products/{{ $product->id }}/img/medium/{{ $product->photo->name }}" @endif>
            </div>
          </div>

          

          {{-- Чекбокс отображения на сайте --}}
          @can ('publisher', $product)
          <div class="small-12 cell checkbox">
            {{ Form::checkbox('display', 1, $product->display, ['id' => 'display']) }}
            <label for="display"><span>Отображать на сайте</span></label>
          </div>
          @endcan

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

        </div>
      </div>

      {{ Form::close() }}

      <!-- Свойства -->
      <div class="tabs-panel" id="properties">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-8 cell">
            <table>
              <thead>
                <tr> 
                  <th>Название</th>
                  <th>Максимум</th>
                  <th>Минимум</th>
                  <th>Подтверждение</th>
                  <th>Отрицание</th>
                  <th>Цвет</th>
                  <th>Список</th>
                  <th></th>
                </tr>
              </thead>
              <tbody id="metrics-table">
                {{-- Таблица метрик товара --}}
                @if (!empty($product->metrics))
                @each('products.metric', $product->metrics, 'metric')
                @endif
              </tbody>
            </table>
          </div>
          <div class="small-12 medium-4 cell">
            {{ Form::open(['id' => 'properties-form','data-abide', 'novalidate']) }}
            <fieldset>
              <legend>Добавить свойство | <a data-toggle="properties-dropdown">Метрики</a></legend>
              
              <label>Выберите свойство
                {{ Form::select('property_id', $properties_list, null, ['id' => 'properties-select']) }}
              </label>
              <div class="grid-x grid-padding-x" id="property-form"></div>

            </fieldset>
            {{ Form::hidden('product_id', $product->id) }}
            {{ Form::close() }}
            {{-- Список свойств с метриками --}}
            <div class="dropdown-pane" id="properties-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">
              @include('products.properties-list', $properties)
            </div>
            
          </div>
        </div>
      </div>

      

      <!-- Состав -->
      <div class="tabs-panel" id="compositions">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-5 cell">

          </div>
        </div>
      </div>

      <!-- Фотографии -->
      <div class="tabs-panel" id="photos">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-5 cell">

            {{-- Форма редактированя фотки --}}
            {{ Form::open(['url' => '/product/edit_photo', 'data-abide', 'novalidate', 'id' => 'form-photo-edit']) }}

            @include('products.photo-edit', ['photo' => $photo])

            {{ Form::hidden('name', $product->name) }}
            {{ Form::hidden('id', $product->id) }}

            {{ Form::close() }}

          </div>
          <div class="small-12 medium-7 cell">
            {{ Form::open(['url' => '/product/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}

            {{ Form::hidden('name', $product->name) }}
            {{ Form::hidden('id', $product->id) }}

            {{ Form::close() }}

            <ul class="grid-x small-up-4 tabs-margin-top" id="photos-list">
              @if (isset($product->album_id))
              @foreach ($product->album->photos as $photo)
              @include('products.photos-list', ['photo' => $photo])
              
              @endforeach
              @endif

            </ul>

          </div>
        </div>
      </div>



    </div>
  </div>
</div>


<div class="grid-x grid-padding-x inputs">


  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit('Редактировать продукцию', ['class'=>'button', 'form' => 'product-form']) }}
  </div>
</div>
@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@php
$settings = config()->get('settings');
@endphp
<script>

  // Основные ностойки
  var product_id = '{{ $product->id }}';

  // При клике на удаление метрики со страницы
  $(document).on('click', '[data-open="item-delete-ajax"]', function() {

    // Находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var id = parent.attr('id').split('-')[1];

    // Удаляем элемент со страницы
    $('#metrics-' + id).remove();

    // Убираем отмеченный чекбокс в списке метрик
    $('#add-metric-' + id).prop('checked', false);
  });

  // При смнене свойства в select
  $(document).on('change', '#properties-select', function() {
    // alert($(this).val());

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/add_property',
      type: 'GET',
      data: {id: $(this).val(), entity: 'products'},
      success: function(html){
        // alert(html);
        $('#property-form').html(html);
      }
    })
  });

  // При клике на кнопку под Select'ом свойств
  $(document).on('click', '#add-metric', function(event) {
    event.preventDefault();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/add_product_metric',
      type: 'POST',
      data: $('#properties-form').serialize(),
      success: function(html){
        // alert(html);
        $('#metrics-table').append(html);
        $('#property-form').html('');

        // В случае успеха обновляем список метрик
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/products/' + product_id + '/edit',
          type: 'GET',
          data: $('#product-form').serialize(),
          success: function(html){
            // alert(html);

            $('#properties-dropdown').html(html);
          }
        })
      }
    })
  });

  // При клике на чекбокс метрики отображаем ее на странице
  $(document).on('click', '.add-metric', function() {

    // alert($(this).val());
    var id = $(this).val();
    
    // Если нужно добавить метрику
    if ($(this).prop('checked') == true) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_metric',
        type: 'POST',
        data: {id: $(this).val(), entity: 'products'},
        success: function(html){

          // alert(html);
          $('#metrics-table').append(html);
          $('#property-form').html('');
        }
      })
    } else {

      // Если нужно удалить метрику со страницы
      $('#metrics-' + id).remove();
    }
  });

  // При клике на свойство отображаем или скрываем его метрики
  $(document).on('click', '.parent', function() {

    // Скрываем все метрики
    $('.checker-nested').hide();

    // Показываем нужную
    $('#' +$(this).data('open')).show();
  });

  // При клике на фотку подствляем ее значения в блок редактирования
  $(document).on('click', '#photos-list img', function(event) {
    event.preventDefault();

    // Удаляем всем фоткам активынй класс
    $('#photos-list img').removeClass('active');

    // Наваливаем его текущей
    $(this).addClass('active');
    
    var id = $(this).data('id');

    // Получаем инфу фотки
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/get_photo',
      type: 'GET',
      data: {id: id, entity: 'products'},
      success: function(html){

        // alert(html);
        $('#form-photo-edit').html(html);
        // $('#first-add').foundation();
        // $('#first-add').foundation('open');
      }
    })
  });

  // При сохранении информации фотки
  $(document).on('click', '#form-photo-edit .button', function(event) {
    event.preventDefault();

    var id = $(this).closest('#form-photo-edit').find('input[name=id]').val();
    // alert(id);

    // Записываем инфу и обновляем
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/update_photo/' + id,
      type: 'PATCH',
      data: $(this).closest('#form-photo-edit').serialize(),
      success: function(html){
        // alert(html);
        $('#form-photo-edit').html(html);
        
        // $('#first-add').foundation();
        // $('#first-add').foundation('open');
      }
    })
  });

  // НАстройки dropzone
  var minImageHeight = 795;
  Dropzone.options.myDropzone = {
    paramName: 'photo',
    maxFilesize: {{ $settings['img_max_size']->value }}, // MB
    maxFiles: 20,
    acceptedFiles: '{{ $settings['img_formats']->value }}',
    addRemoveLinks: true,
    init: function() {
      this.on("success", function(file, responseText) {
        file.previewTemplate.setAttribute('id',responseText[0].id);

        // alert(file);
      });
      this.on("thumbnail", function(file) {
        if (file.width < {{ $settings['img_min_width']->value }} || file.height < minImageHeight) {
          file.rejectDimensions();
        } else {
          file.acceptDimensions();
        }
      });
    },
    accept: function(file, done) {
      file.acceptDimensions = done;
      file.rejectDimensions = function() { done("Размер фото мал, нужно минимум {{ $settings['img_min_width']->value }} px в ширину"); };
    }
  };

</script>
@endsection