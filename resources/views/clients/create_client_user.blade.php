@extends('layouts.app')

@section('inhead')
@include('includes.scripts.class.city_search')
@endsection

@section('title', 'Новый клиент')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОГО КЛИЕНТА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'clients.storeUser', 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => 'true']) }}
    @include('users.form', ['submitButtonText' => 'Добавить клиента', 'param' => ''])
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



