@extends('layouts.app')

@section('title', 'Новый альбом')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ НОВОГО АЛЬБОМА</h2>

    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'albums.store', 'data-abide', 'novalidate']) }}
@include('albums.form', ['submit_text' => 'Добавить', 'form' => null])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('albums.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'albums'])
@endsection



