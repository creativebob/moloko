@extends('layouts.app')

@section('title', 'Новый сайт')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ нового сайта</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'sites.store', 'data-abide', 'novalidate']) }}
@include('sites.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}

@endsection








