@extends('layouts.app')

@section('title', 'Новый список рассылки')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ Нового списка рассылки</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::open(['route' => 'mailing_lists.store', 'data-abide', 'novalidate']) }}
        @include('system.pages.marketings.mailing_lists.form', ['submitText' => 'Добавить'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
