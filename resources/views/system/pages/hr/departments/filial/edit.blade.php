@extends('layouts.app')

@section('title', 'Редактирование филиала')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $department->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">Редактирование филиала</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::model($department, ['route' => ['departments.update', $department->id], 'id' => 'form-edit', 'data-abide', 'novalidate']) }}
    @method('PATCH')
    @include('system.pages.hr.departments.filial.form', ['submitButtonText' => 'Редактировать филиал'])

    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('system.pages.hr.positions.scripts')
@endpush




