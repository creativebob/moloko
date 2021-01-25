@extends('layouts.app')

{{--@section('inhead')--}}
{{--    @include('includes.scripts.dropzone-inhead')--}}
{{--@endsection--}}

@section('title', 'Новая фотография')

@section('breadcrumbs', Breadcrumbs::render('album-section-create', $album, $pageInfo))

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
            {!! Form::open(['route' => ['photos.store', $album->id], 'data-abide', 'novalidate', 'files'=>'true', 'class'=> 'dropzone', 'id' => 'my-dropzone']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('modals')
    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
    <script>
        const imgMaxSize = parseInt({{ $settings['img_max_size'] }}),
            imgFormats = '{{ $settings['img_formats'] }}',
            strictMode = parseInt({{ $settings['strict_mode'] }}),
            imgMinWidth = parseInt({{ $settings['img_min_width'] }}),
            imgMinHeight = parseInt({{ $settings['img_min_height'] }});

        window.dropzone.options.myDropzone = {
            paramName: 'photo',
            maxFiles: 20,
            addRemoveLinks: true,

            dictDefaultMessage: "Перетащите сюда фотографии или кликните по этому полю",
            dictCancelUpload: "Прервать загрузку",
            dictUploadCanceled: "Загрузка прервана",
            dictRemoveFile: "Удалить",

            maxFilesize: imgMaxSize,
            acceptedFiles: '.jpg,.jpeg',
            init: function () {
                this.on("thumbnail", function (file) {
                    if (strictMode == 0) {
                        if (file.width < imgMinWidth || file.height < imgMinHeight) {
                            file.rejectDimensions()
                        } else {
                            file.acceptDimensions();
                        }
                    } else {
                        if (file.width != imgMinWidth || file.height != imgMinHeight) {
                            file.rejectDimensions()
                        } else {
                            file.acceptDimensions();
                        }
                    }
                });
                this.on("error", function (file, message) {
                    alert(message);
                    this.removeFile(file);
                });
                this.on("success", function (file, responseText) {
                    file.previewTemplate.setAttribute('id', responseText[0].id);
                });
            },
            accept: function (file, done) {
                file.acceptDimensions = done;
                file.rejectDimensions = function () {
                    if (strictMode == 0) {
                        done("Размер фото мал, нужно минимум " + imgMinWidth + " px в ширину, и " + imgMinHeight + " в высоту.");
                    } else {
                        done("Размер должен быть " + imgMinWidth + " px в ширину, и " + imgMinHeight + " в высоту.");
                    }
                };
            }
        }

        console.log(window.dropzone.options.myDropzone);
    </script>
@endpush



