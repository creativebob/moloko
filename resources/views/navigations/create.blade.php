@extends('layouts.app')

@section('title', 'Новая навигация')

@section('breadcrumbs', Breadcrumbs::render('site-section-create', $site, $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ Навигации</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => ['navigations.store', $site_id], 'data-abide', 'novalidate']) }}
@include('navigations.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@endsection



