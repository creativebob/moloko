@extends('layouts.app')


@section('title', 'Новый пользователь')

@section('breadcrumbs', Breadcrumbs::render('site-section-create', $site, $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::open(['route' => ['users.store', $site->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
    @include('system.pages.marketings.users.form', ['submitButtonText' => 'Добавить пользователя'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.delete-from-page-script')
    @include('system.pages.marketings.users.scripts')
@endpush



