@extends('layouts.app')

@section('title', 'Новый показатель')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ показателя</h2>

    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'indicators.store', 'data-abide', 'novalidate']) }}
@include('indicators.form', ['submit_text' => 'Добавить', 'form' => null])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('indicators.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'indicators'])

@include('includes.scripts.get_units')
@endsection



