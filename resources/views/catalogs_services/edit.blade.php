@extends('layouts.app')

@section('title', 'Редактировать каталог')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $catalogs_service->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ каталог "{{ $catalogs_service->name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($catalogs_service, ['route' => ['catalogs_services.update', $catalogs_service->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('catalogs_services.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('catalogs_services.scripts')
@include('includes.scripts.upload-file')
@endsection


