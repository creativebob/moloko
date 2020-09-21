@extends('layouts.app')

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
    {{ Form::model($company, ['route' => ['manufacturers.update', $manufacturer->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
    @include('system.pages.companies.form', ['submitButtonText' => 'Редактировать производителя'])
    {{ Form::close() }}
@endsection

@section('modals')
    <section id="modal"></section>
    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.modal-delete-script')
    @include('includes.scripts.extra-phone')
    @include('includes.bank_accounts.bank-account-script', ['id' => $manufacturer->company->id])
@endpush

