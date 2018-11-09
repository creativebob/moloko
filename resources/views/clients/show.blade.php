@extends('layouts.app')

@section('title', 'Просмотр дилера')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР ДИЛЕРА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($company, ['route' => ['companies.update', $company->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать дилера', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection



