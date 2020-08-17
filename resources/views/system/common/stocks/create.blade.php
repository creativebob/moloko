@extends('layouts.app')

@section('title', 'Новый склад')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ НОВОГО склада</h2>

    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'stocks.store', 'data-abide', 'novalidate']) }}
@include('stocks.form', ['submit_text' => 'Добавить', 'form' => null])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('stocks.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'stocks'])
@endsection



