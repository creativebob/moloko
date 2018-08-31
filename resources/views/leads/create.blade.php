@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новый лид')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">СОЗДАНИЕ НОВОГО ЛИДА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/leads', 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files'=>'true']) }}
    @include('leads.form', ['submitButtonText' => 'Добавить лида', 'param' => '', 'readonly'=>'', 'autofocus'=>'autofocus'])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
  @include('includes.scripts.upload-file')
  @include('includes.scripts.delete-from-page-script')
  @include('leads.scripts')
@endsection



