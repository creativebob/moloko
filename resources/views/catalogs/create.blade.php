@extends('layouts.app')

@section('title', 'Новый каталог')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">ДОБАВЛЕНИЕ НОВого каталога</h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')
{{ Form::open([
	'route' => ['catalogs.store'],
	'data-abide',
	'novalidate',
	'files' => 'true'
]
) }}
@include('catalogs.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}
@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('catalogs.scripts')
@include('includes.scripts.upload-file')
@endsection






