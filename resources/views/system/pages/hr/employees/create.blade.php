@extends('layouts.app')

@section('title', 'Новый сотрудник')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">СОЗДАНИЕ НОВОГО СОТРУДНИКА</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::open(['route' => 'employees.store', 'data-abide', 'novalidate', 'files' => 'true']) }}
    @include('system.pages.marketings.users.form', ['submitButtonText' => 'Добавить сотрудника'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.delete-from-page-script')
    @include('system.pages.marketings.users.scripts')
@endpush



