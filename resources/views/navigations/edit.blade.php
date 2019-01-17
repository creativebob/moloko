@extends('layouts.app')

@section('title', 'Редактировать группу товаров')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $navigation->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАНИЕ НАвигации</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($navigation, ['route' => ['navigations.update', $navigation->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}
@include('navigations.form', ['submit_text' => 'Редактировать'])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'navigations'])
@endsection