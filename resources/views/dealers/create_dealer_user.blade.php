@extends('layouts.app')

@section('inhead')
@include('includes.scripts.class.city_search')
@endsection

@section('title', 'Новый дилер')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОГО ДИЛЕРА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'dealers.storeUser', 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => 'true']) }}
    @include('system.pages.marketings.users.form', ['submitButtonText' => 'Добавить дилера', 'param' => ''])
  {{ Form::close() }}

@endsection

@section('modals')
    {{-- Модалка добавления роли --}}
    @include('includes.modals.modal-add-role')

    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
    @include('system.pages.marketings.users.scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.delete-from-page-script')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.extra-phone')
@endpush



