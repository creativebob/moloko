@extends('layouts.app')

@section('title', 'Редактировать список рассылки')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $mailingList->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ список рассылки &laquo{{ $mailingList->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($mailingList, ['route' => ['mailing_lists.update', $mailingList->id], 'data-abide', 'novalidate']) }}
        @method('PATCH')
        @include('system.pages.marketings.mailing_lists.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
