@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать компанию')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $company->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ КОМПАНИЮ</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::model($company, ['url' => '/admin/companies/'.$company->id, 'data-abide', 'novalidate', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать компанию', 'param'=>''])
    {{ Form::close() }}
@endsection

@section('modals')
    <section id="modal"></section>
    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
    @include('companies.scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.modal-delete-script')
    @include('includes.scripts.extra-phone')
    @include('includes.bank_accounts.bank-account-script', ['id' => $company->id])
@endsection


