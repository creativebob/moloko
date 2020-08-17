@extends('layouts.app')

@section('title', 'Редактировать рубрикатор')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $rubricator->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ рубрикатор "{{ $rubricator->name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($rubricator, ['route' => ['rubricators.update', $rubricator->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('rubricators.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('rubricators.scripts')
@include('includes.scripts.upload-file')
@endsection


