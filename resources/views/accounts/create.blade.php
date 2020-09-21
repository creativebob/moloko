@extends('layouts.app')

@section('title', 'Новый аккаунт')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОГО АККАУНТА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/accounts', 'data-abide', 'novalidate', 'class' => '']) }}
    @include('accounts.form', ['submitButtonText' => 'Добавить аккаунт', 'param' => ''])
  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.source_services')

  @endpush



