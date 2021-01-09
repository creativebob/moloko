@extends('layouts.app')

@section('title', 'Редактировать каталог')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $catalogServices->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ каталог "{{ $catalogServices->name }}"</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($catalogServices, ['route' => ['catalogs_services.update', $catalogServices->id], 'data-abide', 'novalidate', 'files' => 'true']) !!}
    @method('PATCH')
    @include('system.pages.catalogs.services.catalogs_services.form', ['submitText' => 'Редактировать'])
    {!! Form::close() !!}
@endsection



