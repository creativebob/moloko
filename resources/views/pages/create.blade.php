@extends('layouts.app')

@section('title', 'Новая страница')

@section('breadcrumbs', Breadcrumbs::render('section-create', $parent_page_info, $site, $page_info))

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
  {{ Form::open(['url' => '/admin/sites/'.$alias.'/pages', 'data-abide', 'novalidate', 'files'=>'true']) }}
    @include('pages.form', ['submitButtonText' => 'Добавить страницу', 'param' => ''])
  {{ Form::close() }}
@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('pages.scripts')
  @include('includes.scripts.upload-file')
@endsection






