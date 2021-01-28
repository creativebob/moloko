@extends('layouts.app')

@section('title', 'Редактировать настройку фотографий')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $photoSetting->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ настройку фотографий</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($photoSetting, ['route' => ['photo_settings.update', $photoSetting->id], 'data-abide', 'novalidate']) !!}
    @method('PATCH')
    @include('system.pages.settings.photo_settings.form', ['submitText' => 'Редактировать'])
    {!! Form::close() !!}
@endsection
