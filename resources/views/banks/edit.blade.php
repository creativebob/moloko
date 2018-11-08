@extends('layouts.app')

@section('title', 'Редактировать банк')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $bank->company->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ БАНК</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($bank, ['url' => '/admin/banks/'.$bank->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('banks.form', ['submitButtonText' => 'Редактировать банк', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


