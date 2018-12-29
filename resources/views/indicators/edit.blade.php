@extends('layouts.app')

@section('title', 'Редактировать альбом')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $indicator))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ показатель</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($indicator, ['route' => ['indicators.update', $indicator->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@include('indicators.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('indicators.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'indicators'])

@include('includes.scripts.get_units')
@endsection