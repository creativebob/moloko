@extends('layouts.app')

@section('title', 'Новая страница')

@section('breadcrumbs', Breadcrumbs::render('site-section-create', $site, $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ страницы</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
{{ Form::open(['route' => ['pages.store', $site_id], 'data-abide', 'novalidate', 'files'=>'true']) }}
@include('pages.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}
@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('pages.scripts')
@include('includes.scripts.upload-file')

@include('includes.scripts.ckeditor')
@endsection






