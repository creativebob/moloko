@extends('layouts.app')

@section('title', 'Редактировать фотографию')

@section('breadcrumbs', Breadcrumbs::render('album-section-edit', $album, $pageInfo, $photo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАние фотографии "{{ $photo->name }}"</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($photo, ['route' => ['photos.update', [$album->id, $photo->id]], 'data-abide', 'novalidate']) !!}
    @method('PATCH')

    <div class="grid-x grid-padding-x inputs tabs-margin-top">
        <div class="small-12 medium-6 cell">
            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-12 cell">
                    <label>Заголовок фото
                        @include('includes.inputs.name', ['name' => 'title', 'value' => $photo->title, 'required' => true])
                    </label>
                    <label>Описание
                        @include('includes.inputs.textarea', ['name' => 'description', 'value' => $photo->description])
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Ссылка на внешний адрес (В случае необходимости)
                        @include('includes.inputs.link', ['name' => 'link', 'value' => $photo->link])
                    </label>
                </div>

                <div class="small-12 medium-6 cell">
                    <label for="head">Цвет (В случае необходимости)

                        <input type="color" id="head" name="color"
                               value="{{ isset($photo->color) ? $photo->color : '#e66465' }}"/>
                    </label>
                </div>
            </div>
        </div>

        <div class="small-12 medium-6 cell text-center checkbox">
            <img id="photo" src="{{ getPhotoInAlbumPath($photo) }}">
        </div>

        <div class="small-12 small-text-center cell checkbox">
            {!! Form::hidden('is_avatar', 0) !!}
            {{ Form::checkbox('is_avatar', 1, null, ['id' => 'avatar-checkbox']) }}
            <label for="avatar-checkbox"><span>Сделать аватаром альбома</span></label>
        </div>

        {{-- Чекбоксы управления --}}
        @include('includes.control.checkboxes', ['item' => $photo])

        <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
            {{ Form::submit('Редактировать', ['class' => 'button']) }}
        </div>
    </div>

    {!! Form::close() !!}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush


