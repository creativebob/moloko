@extends('layouts.app')

@section('title', 'Редактировать агента')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $agent->company->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ АГЕНТА</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::model($company, ['route' => ['agents.update', $agent->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
    @include('system.pages.companies.form', ['submitButtonText' => 'Редактировать агента'])
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
    @include('includes.bank_accounts.bank-account-script', ['id' => $agent->company->id])
@endpush

