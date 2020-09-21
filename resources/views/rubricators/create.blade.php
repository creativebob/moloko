@extends('layouts.app')

@section('title', 'Новый рубрикатор')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">ДОБАВЛЕНИЕ НОВого рубрикатора</h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')
{{ Form::open([
	'route' => ['rubricators.store'],
	'data-abide',
	'novalidate',
	'files' => 'true'
]
) }}
@include('rubricators.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}
@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('rubricators.scripts')
@include('includes.scripts.upload-file')
@endpush






