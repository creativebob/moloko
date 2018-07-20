@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@include('includes.scripts.sortable-inhead')
@endsection

@section('title', 'Редактирование категории услуг')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $services_category->name))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
    <h2 class="header-content">Редактирование категории услуг &laquo{{ $services_category->name }}&raquo</h2>
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
      <li class="tabs-title"><a data-tabs-target="site" href="#site">Сайт</a></li>

        {{--
      <li class="tabs-title"><a data-tabs-target="properties" href="#properties">Свойства</a></li>
      <li class="tabs-title"><a data-tabs-target="compositions" href="#compositions">Состав</a></li>
      <li class="tabs-title"><a data-tabs-target="price-rules" href="#price-rules">Ценообразование</a></li>
      --}}
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

      {{ Form::model($services_category, ['url' => '/admin/services_categories/'.$services_category->id, 'data-abide', 'novalidate', 'files'=>'true', 'id' => 'products-category-form']) }}
      {{ method_field('PATCH') }}

      <!-- Общая информация -->
      <div class="tabs-panel is-active" id="options">
        <div class="grid-x grid-padding-x">

          <div class="small-12 medium-6 cell">
            <label>Название категории
              @include('includes.inputs.name', ['value'=>$services_category->name, 'name'=>'name', 'required'=>'required'])
            </label>
          </div>

          {{-- Чекбокс отображения на сайте --}}
          @can ('publisher', $services_category)
          <div class="small-12 cell checkbox">
            {{ Form::checkbox('display', 1, $services_category->display, ['id' => 'display']) }}
            <label for="display"><span>Отображать на сайте</span></label>
          </div>
          @endcan

          {{-- Чекбокс модерации --}}
          @can ('moderator', $services_category)
          @if ($services_category->moderation == 1)
          <div class="small-12 cell checkbox">
            @include('includes.inputs.moderation', ['value'=>$services_category->moderation, 'name'=>'moderation'])
          </div>
          @endif
          @endcan

          {{-- Чекбокс системной записи --}}
          @can ('god', $services_category)
          <div class="small-12 cell checkbox">
            @include('includes.inputs.system', ['value'=>$services_category->system_item, 'name'=>'system_item']) 
          </div>
          @endcan

          {{-- Кнопка --}}
          <div class="small-12 cell tabs-button tabs-margin-top">
            {{ Form::submit('Редактировать услугу', ['class'=>'button']) }}
          </div>
        </div>
      </div>

      <!-- Сайт -->
      <div class="tabs-panel" id="site">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-6 cell">

            <label>Описание:
              {{ Form::textarea('description', $services_category->description, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
            </label><br>

            <label>Description для сайта
              @include('includes.inputs.textarea', ['value'=>$services_category->seo_description, 'name'=>'seo_description', 'required'=>''])
            </label>

          </div>
          <div class="small-12 medium-6 cell">
            <label>Выберите аватар
              {{ Form::file('photo') }}
            </label>
            <div class="text-center">
             <img id="photo" @if (isset($services_category->photo_id)) src="/storage/{{ $services_category->company->id }}/media/services_categories/{{ $services_category->id }}/img/medium/{{ $services_category->photo->name }}" @endif>
           </div>
         </div>

         {{-- Кнопка --}}
         <div class="small-12 cell tabs-button tabs-margin-top">
          {{ Form::submit('Редактировать категорию услуг', ['class'=>'button']) }}
        </div>
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
                <th>Минимум</th>
                <th>Максимум</th>
                <th>Подтверждение</th>
                <th>Отрицание</th>
                <th>Цвет</th>
                <th>Список</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="metrics-table">
              {{-- Таблица метрик товара --}}
              @if (!empty($services_category->metrics))
              @each('services_categories.metrics.metric', $services_category->metrics, 'metric')
              @endif
            </tbody>
          </table>
        </div>
        <div class="small-12 medium-4 cell">
          {{ Form::open(['url' => '/add_services_category_metric', 'id' => 'properties-form', 'data-abide', 'novalidate']) }}
          <fieldset>
            <legend><a data-toggle="properties-dropdown">Добавить метрику</a></legend>

            <div class="grid-x grid-padding-x" id="property-form"></div>

          </fieldset>
          {{ Form::hidden('entity_id', $services_category->id) }}
          {{ Form::close() }}
          {{-- Список свойств с метриками --}}
          <div class="dropdown-pane" id="properties-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">
            @include('services_categories.metrics.properties-list', $properties)
          </div>

        </div>
      </div>
    </div>

    {{-- Исключаем состав из сырья --}}

    <!-- Состав -->
    <div class="tabs-panel" id="compositions">
      <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">
          <table class="composition-table">
            <thead>
              <tr> 
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody id="composition-table">
              {{-- Таблица метрик товара --}}
              @if (!empty($services_category->compositions))
              @each('services_categories.compositions.composition', $services_category->compositions, 'composition')
              @endif
            </tbody>
          </table>
        </div>

        <div class="small-12 medium-3 cell">

          <ul class="menu vertical">

            @foreach ($services_modes_list as $services_mode)
            <li>
              <a class="button" data-toggle="{{ $services_mode['alias'] }}-dropdown">{{ $services_mode['name'] }}</a>
              <div class="dropdown-pane" id="{{ $services_mode['alias'] }}-dropdown" data-dropdown data-position="bottom" data-alignment="left" data-close-on-click="true">

                <ul class="checker" id="products-categories-list">
                  @foreach ($services_mode['services_categories'] as $services_cat)
                  @include('services_categories.compositions.services-category', $services_cat)
                  @endforeach
                </ul>

              </div>
            </li>
            @endforeach
          </ul>

        </div>

      </div>
    </div>


  </div>
</div>



@endsection

@section('scripts')

@include('includes.scripts.inputs-mask')
@include('includes.scripts.upload-file')
@include('products.scripts')
@php
$settings = config()->get('settings');
@endphp

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>


  CKEDITOR.replace('content-ckeditor');

  // Конфигурация 
  CKEDITOR.config.toolbar = [
    ['Bold', 'Italic', 'NumberedList', 'BulletedList', 'Maximize', 'Source']
  ];



  // Основные ностойки
  var services_category_id = '{{ $services_category->id }}';

  // При клике на удаление метрики со страницы
  $(document).on('click', '[data-open="delete-metric"]', function() {

    // Находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var id = parent.attr('id').split('-')[1];

    // alert(id);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/ajax_delete_relation_metric',
      type: 'POST',
      data: {id: id, entity: 'services_categories', entity_id: services_category_id},
      success: function(date){

        var result = $.parseJSON(date);
          // alert(result);

          if (result['error_status'] == 0) {

            // Удаляем элемент со страницы
            $('#metrics-' + id).remove();

            // В случае успеха обновляем список метрик
            // $.ajax({
            //   headers: {
            //     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //   },
            //   url: '/products/' + product_id + '/edit',
            //   type: 'GET',
            //   data: $('#product-form').serialize(),
            //   success: function(html){
            //     // alert(html);
            //     $('#properties-dropdown').html(html);
            //   }
            // })

            // Убираем отмеченный чекбокс в списке метрик
            $('#add-metric-' + id).prop('checked', false);
            
          } else {
            alert(result['error_message']);
          }; 
        }
      })
  });

  // При клике на удаление состава со страницы
  $(document).on('click', '[data-open="delete-composition"]', function() {

    // Находим описание сущности, id и название удаляемого элемента в родителе
    var parent = $(this).closest('.item');
    var id = parent.attr('id').split('-')[1];

    // alert(id);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/ajax_delete_relation_composition',
      type: 'POST',
      data: {id: id, services_category_id: services_category_id},
      success: function(date){

        var result = $.parseJSON(date);
        // alert(result);

        if (result['error_status'] == 0) {

            // Удаляем элемент со страницы
            $('#compositions-' + id).remove();

            // Убираем отмеченный чекбокс в списке метрик
            $('#add-product-' + id).prop('checked', false);
            
          } else {
            alert(result['error_message']);
          }; 
        }
      })
  });

  // При клике на удаление состава со страницы
  $(document).on('click', '[data-open="delete-value"]', function() {

    // Удаляем элемент со страницы
    $(this).closest('.item').remove();
  });

  // Смена категории едениц измерения
  $(document).on('change', '#units-categories-list', function() {
    var id = $(this).val();
    // alert(id);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/get_units_list',
      type: "POST",
      data: {id: id, entity: 'services_categories'},
      success: function(html){
        $('#units-list').html(html);
        $('#units-list').prop('disabled', false);
      }
    }); 
  });

  // При смнене свойства в select
  $(document).on('change', '#properties-select', function() {
    // alert($(this).val());

    var id = $(this).val();

    // Если вернулись на "Выберите свойство" то очищаем форму
    if (id == '') {
      $('#property-form').html('');
    } else {
      // alert(id);
      $('#property-id').val(id);

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_add_property',
        type: 'POST',
        data: {id: id, entity: 'services_categories'},
        success: function(html){
        // alert(html);
        $('#property-form').html(html);
        $('#properties-dropdown').foundation('close');
      }
    })
    }
  });

  // При клике на кнопку под Select'ом свойств
  $(document).on('click', '#add-metric', function(event) {
    event.preventDefault();

    // alert($('#properties-form').serialize());

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/metrics',
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
          url: '/admin/services_categories/' + services_category_id + '/edit',
          type: 'POST',
          success: function(html){
            // alert(html);

            $('#properties-dropdown').html(html);
          }
        })
      }
    })
  });

  // При клике на кнопку под Select'ом свойств
  $(document).on('click', '#add-value', function(event) {
    event.preventDefault();

    // alert($('#properties-form input[name=value]').val());
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/ajax_add_metric_value',
      type: 'POST',
      data: {value: $('#properties-form input[name=value]').val()},
      success: function(html){
        // alert(html);
        $('#values-table').append(html);
        $('#properties-form input[name=value]').val('');
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
        url: '/ajax_add_relation_metric',
        type: 'POST',
        data: {id: $(this).val(), entity: 'services_categories', entity_id: services_category_id},
        success: function(html){

          // alert(html);
          $('#metrics-table').append(html);
          $('#property-form').html('');
        }
      })
    } else {

      // Если нужно удалить метрику
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_delete_relation_metric',
        type: 'POST',
        data: {id: $(this).val(), entity: 'services_categories', entity_id: services_category_id},
        success: function(date){

          var result = $.parseJSON(date);
          // alert(result);

          if (result['error_status'] == 0) {

            $('#metrics-' + id).remove();
          } else {
            alert(result['error_message']);
          }; 
        }
      })
    }
  });

  // При клике на свойство отображаем или скрываем его метрики
  $(document).on('click', '.parent', function() {

    // Скрываем все метрики
    $('.checker-nested').hide();

    // Показываем нужную
    $('#' +$(this).data('open')).show();
  });

  // При клике на чекбокс метрики отображаем ее на странице
  $(document).on('click', '.add-composition', function() {

    var id = $(this).val();
    // alert(services_category_id + ' ' + id);
    
    // Если нужно добавить состав
    if ($(this).prop('checked') == true) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_add_relation_composition',
        type: 'POST',
        data: {id: id, services_category_id: services_category_id, entity: 'services_categories'},
        success: function(html){
          // alert(html);
          $('#composition-table').append(html);
        }
      })
    } else {

      // Если нужно удалить состав
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/ajax_delete_relation_composition',
        type: 'POST',
        data: {id: id, services_category_id: services_category_id, entity: 'services_categories'},
        success: function(date){

          var result = $.parseJSON(date);
          // alert(result);

          if (result['error_status'] == 0) {

            $('#compositions-' + id).remove();
          } else {
            alert(result['error_message']);
          }; 
        }
      })
    }
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
      url: '/ajax_get_photo',
      type: 'POST',
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
      url: '/ajax_update_photo/' + id,
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

  // Оставляем ширину у вырванного из потока элемента
  var fixHelper = function(e, ui) {
    ui.children().each(function() {
      $(this).width($(this).width());
    });
    return ui;
  };

  // Включаем перетаскивание
  $("#values-table tbody").sortable({
    axis: 'y',
    helper: fixHelper, // ширина вырванного элемента
    handle: 'td:first', // указываем за какой элемент можно тянуть
    placeholder: "table-drop-color", // фон вырванного элемента
    update: function( event, ui ) {

      var entity = $(this).children('.item').attr('id').split('-')[0];
    }
  });

  // Настройки dropzone
  var minImageHeight = 795;
  Dropzone.options.myDropzone = {
    paramName: 'photo',
    maxFilesize: {{ $settings['img_max_size'] }}, // MB
    maxFiles: 20,
    acceptedFiles: '{{ $settings['img_formats'] }}',
    addRemoveLinks: true,
    init: function() {
      this.on("success", function(file, responseText) {
        file.previewTemplate.setAttribute('id',responseText[0].id);

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/product/photos',
          type: 'post',
          data: {services_category_id: services_category_id},
          success: function(html){
        // alert(html);
        $('#photos-list').html(html);
        
        // $('#first-add').foundation();
        // $('#first-add').foundation('open');
      }
    })
      });
      this.on("thumbnail", function(file) {
        if (file.width < {{ $settings['img_min_width'] }} || file.height < minImageHeight) {
          file.rejectDimensions();
        } else {
          file.acceptDimensions();
        }
      });
    },
    accept: function(file, done) {
      file.acceptDimensions = done;
      file.rejectDimensions = function() { done("Размер фото мал, нужно минимум {{ $settings['img_min_width'] }} px в ширину"); };
    }
  };

</script>
@endsection