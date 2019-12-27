@extends('layouts.app')

@section('title', 'Новый домен')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ нового домена</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'domains.store', 'data-abide', 'novalidate']) }}
@include('system.pages.domains.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}

@endsection








