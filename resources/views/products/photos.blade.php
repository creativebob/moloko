@extends('layouts.app')

@section('inhead')
@include('includes.scripts.dropzone-inhead')
@include('includes.scripts.fancybox-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))


@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Product::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
@if(count($product->album) > 0)
<ul class="grid-x grid-padding-x small-up-3 medium-up-5 large-up-8" id="makeMeScrollable">
  @foreach($product->album->first()->photos as $photo)
  <li class="cell">
    <a data-fancybox="album" href="/storage/{{ $product->company_id }}/media/products/{{ $product->album->first()->id }}/img/large/{{ $photo->name }}">
      <img src="/storage/{{ $product->company_id }}/media/products/{{ $product->album->first()->id }}/img/small/{{ $photo->name }}" alt="Фото">
    </a>
  </li>
  @endforeach
</ul>
@endif

<div class="grid-x">
  <div class="small-12 cell">
   {{ Form::open(['url' => '/product/add_photo', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}

   {{ Form::hidden('name', $product->name) }}
   {{ Form::hidden('id', $product->id) }}

   {{ Form::close() }}
 </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@section('scripts')
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
