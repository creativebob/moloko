@extends('layouts.app')
@include('companies.inhead')

@section('title', 'Просмотр компании')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР КОМПАНИИ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($companies, ['route' => ['companies.update', $companies->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать компанию', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection
@include('companies.scripts')


