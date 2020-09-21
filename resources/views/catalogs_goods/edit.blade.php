@extends('layouts.app')

@section('title', 'Редактировать каталог')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $catalogs_goods->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ каталог "{{ $catalogs_goods->name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($catalogs_goods, ['route' => ['catalogs_goods.update', $catalogs_goods->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('catalogs_goods.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('catalogs_goods.scripts')
@include('includes.scripts.upload-file')
@endpush


