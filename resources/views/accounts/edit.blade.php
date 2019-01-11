@extends('layouts.app')

@section('title', 'Редактировать аккаунт')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $account->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ДАННЫЕ АККАУНТА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($account, ['url' => '/admin/accounts/'.$account->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('accounts.form', ['submitButtonText' => 'Редактировать аккаунт', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.source_services')
@endsection


