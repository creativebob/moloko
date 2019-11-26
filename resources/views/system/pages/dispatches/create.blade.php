@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая рассылка')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ рассылки</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'dispatches.store', 'data-abide', 'novalidate', 'files' => 'true']) }}
@include('system.pages.dispatches.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}

@endsection








