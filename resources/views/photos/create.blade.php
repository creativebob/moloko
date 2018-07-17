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
<div class="grid-x grid-padding-x">
    <div class="small-12 cell">
        {{ Form::open(['url' => '/admin/albums/'.$alias.'/photos', 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) }}
        {{ Form::close() }}
    </div>
</div>
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

@if ($album->album_settings->img_max_size != null)
@php
$settings['img_max_size'] = $album->album_settings->img_max_size;
@endphp
@endif

@if ($album->album_settings->img_formats != null) 
@php
$settings['img_formats'] = $album->album_settings->img_formats;
@endphp
@endif

@if ($album->album_settings->img_min_width != null) 
@php
$settings['img_min_width'] = $album->album_settings->img_min_width;
@endphp
@endif

@if ($album->album_settings->img_min_height != null) 
@php
$settings['img_min_height'] = $album->album_settings->img_min_height;
@endphp
@endif

@php
$settings['upload_mode'] = $album->album_settings->upload_mode;
@endphp

<script>
    var uploadMode = '{{ $settings['upload_mode'] }}';

    Dropzone.options.myDropzone = {
        paramName: 'photo',
        maxFilesize: {{ $settings['img_max_size'] }}, // MB
        maxFiles: 20,
        acceptedFiles: '{{ $settings['img_formats'] }}',
        addRemoveLinks: true,
        init: function() {
            this.on("success", function(file, responseText) {
                file.previewTemplate.setAttribute('id',responseText[0].id);
            });
            this.on("thumbnail", function(file) {
                if (uploadMode == 0) {
                    if (file.width < {{ $settings['img_min_width'] }} || file.height < {{ $settings['img_min_height'] }}) {
                        file.rejectDimensions()
                    }
                    else {
                        file.acceptDimensions();
                    }
                } else {
                    if (file.width != {{ $settings['img_min_width'] }} || file.height != {{ $settings['img_min_height'] }}) {
                        file.rejectDimensions()
                    }
                    else {
                        file.acceptDimensions();
                    }
                }
            });
        },
        accept: function(file, done) {
            file.acceptDimensions = done;
            file.rejectDimensions = function() {
                if (uploadMode == 0) {
                    done("Размер фото мал, нужно минимум {{ $settings['img_min_width'] }} px в ширину, и {{ $settings['img_min_height'] }} в высоту."); 
                } else {
                    done("Размер должен быть {{ $settings['img_min_width'] }} px в ширину, и {{ $settings['img_min_height'] }} в высоту."); 
                }
            };
        }
    };
</script>
@endsection



