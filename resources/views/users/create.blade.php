@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новый пользователь')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'users.store', 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files'=>'true']) }}
    @include('users.form', ['submitButtonText' => 'Добавить пользователя', 'param' => '', 'form' => null])
  {{ Form::close() }}

@endsection

@section('modals')
  {{-- Модалка удаления с ajax --}}
  @include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
  @include('includes.scripts.upload-file')
@endsection



