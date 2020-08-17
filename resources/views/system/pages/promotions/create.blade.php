@extends('layouts.app')

@section('title', 'Новое продвижение')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ нового продвижения</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'promotions.store', 'data-abide', 'novalidate', 'files' => 'true']) }}
@include('system.pages.promotions.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}

@endsection








