@extends('layouts.app')

@section('title', 'Редактировать склад')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $stock->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ склад</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($stock, ['route' => ['stocks.update', $stock->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@include('stocks.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('stocks.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'stocks'])
@endsection
