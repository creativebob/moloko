@extends('layouts.app')

@section('inhead')
	@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новый пост')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
	<div class="top-bar-left">
		<h2 class="header-content">ДОБАВЛЕНИЕ поста</h2>
	</div>
	<div class="top-bar-right">
	</div>
</div>
@endsection

@section('content')
{{ Form::open(['url' => '/admin/posts', 'data-abide', 'novalidate', 'files'=>'true']) }}
@include('posts.form', ['submitButtonText' => 'Добавить пост', 'param' => ''])
{{ Form::close() }}
@endsection

@section('modals')
{{-- Модалка добавления альбома --}}
@include('includes.modals.modal-add-album')
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
	@include('includes.scripts.inputs-mask')
	@include('posts.scripts')
	@include('includes.scripts.pickmeup-script')
	@include('includes.scripts.upload-file')
	@include('includes.scripts.delete-from-page-script')
@endsection






