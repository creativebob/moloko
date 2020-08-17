@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.class.city_search')
@endsection

@section('title', 'Редактировать производителя')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $manufacturer->company->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ ПРОИЗВОДИТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($manufacturer->company, ['url' => '/admin/manufacturers/'.$manufacturer->id, 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать производителя', 'param'=>'', 'company'=>$manufacturer->company])
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
    @include('includes.bank_accounts.bank-account-script', ['id' => $manufacturer->company->id])
@endsection

