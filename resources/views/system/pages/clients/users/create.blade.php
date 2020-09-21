@extends('layouts.app')

@section('title', 'Новый клиент')

@section('breadcrumbs', Breadcrumbs::render('create-client', $pageInfo, 'createClientUser'))

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
    {{ Form::open(['route' => 'clients.storeClientUser', 'data-abide', 'novalidate','files' => 'true']) }}
        @include('system.pages.marketings.users.form', ['submitButtonText' => 'Добавить клиента'])
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
