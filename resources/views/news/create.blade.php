@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая новость')

@section('breadcrumbs', Breadcrumbs::render('section-create', $parent_page_info, $site, $page_info))

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
{{ Form::open(['route' => ['news.store', $site->alias], 'data-abide', 'novalidate', 'files' => 'true']) }}
@include('news.form', ['submit_text' => 'Добавить'])
{{ Form::close() }}
@endsection

@section('modals')
<section id="modal"></section>
{{-- Модалка удаления с ajax --}}
@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
@include('includes.scripts.ckeditor')
@include('includes.scripts.inputs-mask')
@include('news.scripts')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')
@include('includes.scripts.delete-from-page-script')
@endsection






