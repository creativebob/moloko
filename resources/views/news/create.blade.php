@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая страница')

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
{{ Form::open(['url' => '/sites/'.$site->alias.'/news', 'data-abide', 'novalidate', 'files'=>'true']) }}
@include('news.form', ['submitButtonText' => 'Добавить новость', 'param' => ''])
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
@include('news.scripts')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')
@include('includes.scripts.delete-from-page-script')
@endsection






