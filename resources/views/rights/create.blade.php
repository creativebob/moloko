@extends('layouts.app')

@section('title', 'Новый пользователь')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">СОЗДАНИЕ НОВОГО ПРАВИЛА ДОСТУПА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'rights.store', 'data-abide', 'novalidate']) }}
    @include('rights.form', ['submitButtonText' => 'Добавить в систему правило', 'param' => ''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection

