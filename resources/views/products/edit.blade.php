@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
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

  {{ Form::model($product, ['route' => ['products.update', $product->id], 'data-abide', 'novalidate', 'files'=>'true']) }}
  {{ method_field('PATCH') }}

    @include('products.form', ['submitButtonText' => 'Редактировать продукцию', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.upload-file')
  <script>
  var minImageWidth = 1200,
  minImageHeight = 795;
  Dropzone.options.myDropzone = {
    paramName: 'photo',
    maxFilesize: 8, // MB
    maxFiles: 20,
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    addRemoveLinks: true,
    init: function() {
      this.on("success", function(file, responseText) {
        file.previewTemplate.setAttribute('id',responseText[0].id);
      });
      this.on("thumbnail", function(file) {
        if (file.width < minImageWidth || file.height < minImageHeight) {
          file.rejectDimensions()
        }
        else {
          file.acceptDimensions();
        }
      });
    },
    accept: function(file, done) {
      file.acceptDimensions = done;
      file.rejectDimensions = function() { done("Размер фото мал, нужно минимум 1200 px в ширину"); };
    }
  };
</script>
@endsection