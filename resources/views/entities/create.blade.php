@extends('layouts.app')

@section('title', 'Новая сущность')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕГИСТРАЦИЯ НОВОЙ СУЩНОСТИ</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'entities.store', 'data-abide', 'novalidate']) }}

@include('entities.form', ['submit_text' => 'Зарегистрировать'])

{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@endpush



