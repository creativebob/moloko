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
    <h2 class="header-content">РЕДАКТИРОВАТЬ товар</h2>
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
            <label>Единица измерения
              {{ Form::select('unit_id', $units_list, $product->unit_id)}}
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

      <!-- Свойства -->
      <div class="tabs-panel" id="properties">
        <div class="grid-x grid-padding-x">
          <div class="small-12 medium-6 cell">
            <label>Название товара про
            </label>
          </div>
        </div>
      </div>

      {{ Form::close() }}

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
  $(document).on('click', '#photos-list img', function(event) {
    event.preventDefault();

    $('#photos-list img').removeClass('active');
    
    var id = $(this).data('id');

    $(this).addClass('active');

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

  $(document).on('click', '#form-photo-edit .button', function(event) {
    event.preventDefault();

    var id = $(this).closest('#form-photo-edit').find('input[name=id]').val();

    // alert(id);

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