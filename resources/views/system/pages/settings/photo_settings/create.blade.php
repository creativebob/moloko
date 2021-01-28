@extends('layouts.app')

@section('title', 'Новая настройка фотографий')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">СОЗДАНИЕ НОВой настройки фотографий</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::open(['route' => 'photo_settings.store', 'data-abide', 'novalidate']) !!}
    @include('system.pages.settings.photo_settings.form', ['submitText' => 'Добавить'])
    {!! Form::close() !!}
@endsection



