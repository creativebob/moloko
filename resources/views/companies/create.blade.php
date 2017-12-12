@extends('layouts.app')
@include('users.inhead')

@section('title', 'Новая компания')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ КОМПАНИИ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'companies.store', 'data-abide', 'novalidate']) }}
    @include('companies.form', ['submitButtonText' => 'Добавить компанию', 'param' => ''])
  {{ Form::close() }}

@endsection
@include('companies.scripts')



