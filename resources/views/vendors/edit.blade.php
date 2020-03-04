@extends('layouts.app')

@section('inhead')
@include('includes.scripts.class.city_search')
@endsection

@section('title', 'Редактировать вендора')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $vendor->supplier->company->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ВЕНДОРА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($vendor->supplier->company, ['route' => ['vendors.update', $vendor->id], 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => 'true']) }}
  {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать вендора', 'param'=>'', 'company'=>$vendor->supplier->company])
  {{ Form::close() }}

@endsection

@section('modals')
    <section id="modal"></section>
    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.modal-delete-script')
    @include('includes.scripts.extra-phone')
    @include('includes.bank_accounts.bank-account-script', ['id' => $vendor->company->id])
@endsection


