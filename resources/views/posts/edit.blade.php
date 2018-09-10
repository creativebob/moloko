@extends('layouts.app')

@section('inhead')
	@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать пост')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $post->name))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
    <h2 class="header-content">РЕДАКТИРОВАНИЕ поста "{{ $post->name }}"</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')

{{ Form::model($post, ['url' => '/admin/posts/'.$post->id, 'data-abide', 'novalidate', 'files'=>'true']) }}
{{ method_field('PATCH') }}

@include('posts.form', ['submitButtonText' => 'Редактировать пост', 'param'=>''])

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


