@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая заявка поставщику')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">НОВАЯ ЗАЯВКА ПОСТАВЩИКУ</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/applications', 'data-abide', 'novalidate']) }}

    @include('applications.form', ['submitButtonText' => 'Отправить', 'param' => ''])

  {{ Form::close() }}

@endsection

@push('scripts')
    @include('includes.scripts.ckeditor')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.upload-file')
    @endpush



