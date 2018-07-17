@extends('layouts.app')

@section('title', 'Новый пользователь')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/booklists', 'data-abide', 'novalidate']) }}
    @include('users.form', ['submitButtonText' => 'Добавить пользователя', 'param' => ''])
  {{ Form::close() }}

@endsection
@include('users.scripts')



