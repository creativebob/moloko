@extends('layouts.app')

@section('title', 'Новый пользователь')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

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
    @include('system.pages.marketings.users.form', ['submitButtonText' => 'Добавить пользователя', 'param' => ''])
  {{ Form::close() }}

@endsection
@include('system.pages.marketings.users.scripts')



