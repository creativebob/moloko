@extends('layouts.app')

@section('title', 'Добавление филиала')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">Добавление филиала</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::open(['route' => 'departments.store', 'data-abide', 'novalidate']) }}
        @include('system.pages.hr.departments.filial.form', ['submitButtonText' => 'Добавить филиал'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('system.pages.hr.positions.scripts')
@endpush
