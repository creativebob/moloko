@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@include('includes.scripts.dropzone-inhead')
@endsection

@section('title', 'Новая фотография')

@section('breadcrumbs', Breadcrumbs::render('section-create', $parent_page_info, $album, $page_info))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">добавление новой фотографии</h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')
{{ Form::open(['url' => '/albums/'.$alias.'/photos', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
{{ Form::close() }}
@endsection

@section('modals')
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
@include('includes.scripts.cities-list')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')

@php
$settings = config()->get('settings');
@endphp
<script>
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
    	});
    	this.on("thumbnail", function(file) {
    		if (file.width < {{ $settings['img_min_width']->value }} || file.height < minImageHeight) {
    			file.rejectDimensions()
    		}
    		else {
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



