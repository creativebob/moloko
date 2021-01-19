@extends('layouts.app')

@section('title', 'Редактировать конкурента')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $competitor->company->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ конкурента</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($company, ['route' => ['competitors.update', $competitor->id], 'data-abide', 'novalidate', 'files' => 'true']) !!}
    @method('PATCH')
    @include('system.pages.companies.form', ['submitButtonText' => 'Редактировать'])
    {!! Form::close() !!}
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
    @include('includes.bank_accounts.bank-account-script', ['id' => $competitor->company->id])
@endpush

