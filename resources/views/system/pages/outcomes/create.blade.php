@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая выполненная работа')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ НОВОй выполненной работы</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::open(['route' => 'outcomes.store', 'data-abide', 'novalidate', 'files' => 'true']) }}
        @include('system.pages.outcomes.form', ['submit_text' => 'Добавить'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.check', ['entity' => 'outcomes'])
@endpush
