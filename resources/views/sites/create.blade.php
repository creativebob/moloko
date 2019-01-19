@extends('layouts.app')

@section('title', 'Новый сайт')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

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

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.check')
@include('sites.scripts')
@endsection






