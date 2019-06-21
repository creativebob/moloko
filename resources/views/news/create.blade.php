@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая новость')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">ДОБАВЛЕНИЕ новости</h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')
{{ Form::open([
	'route' => 'news.store',
	'data-abide',
	'novalidate',
	'files' => 'true'
]
) }}
@include('news.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}
@endsection
