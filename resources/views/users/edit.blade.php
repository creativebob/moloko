@extends('layouts.app')

@section('inhead')
	@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать пользователя')

{{--@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $user->second_name.' '.$user->first_name))--}}

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
   <h2 class="header-content">РЕДАКТИРОВАТЬ ПОЛЬЗОВАТЕЛЯ</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')

{{ Form::model($user, ['url' => '/admin/sites/' . $site_id . '/users/' . $user->id, 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('users.form', ['submitButtonText' => 'Редактировать пользователя', 'param'=>''])

{{ Form::close() }}

@endsection

@section('modals')
	{{-- Модалка добавления роли --}}
	@include('includes.modals.modal-add-role')

	{{-- Модалка удаления с ajax --}}
	@include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
	@include('users.scripts')
	@include('includes.scripts.inputs-mask')
	@include('includes.scripts.pickmeup-script')
	@include('includes.scripts.delete-from-page-script')
	@include('includes.scripts.upload-file')
	@include('includes.scripts.extra-phone')
@endsection


