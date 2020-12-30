@extends('layouts.app')

@section('title', 'Новый каталог')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">ДОБАВЛЕНИЕ НОВого каталога</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::open(['route' => ['catalogs_services.store'], 'data-abide', 'novalidate', 'files' => 'true']) !!}
    @include('system.pages.catalogs.services.catalogs_services.form', ['submitText' => 'Добавить'])
    {!! Form::close() !!}
@endsection








