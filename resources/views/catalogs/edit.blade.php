@extends('layouts.app')

@section('title', 'Редактировать каталог')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $catalog->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ каталог "{{ $catalog->name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($catalog, ['route' => ['catalogs.update', $catalog->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('catalogs.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('catalogs.scripts')
@include('includes.scripts.upload-file')
@endsection


