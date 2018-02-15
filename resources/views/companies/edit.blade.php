@extends('layouts.app')

@section('title', 'Редактировать компанию')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ КОМПАНИЮ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($company, ['route' => ['companies.update', $company->id], 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать компанию', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


