@extends('layouts.app')

@section('title', 'Новая группа пользователей')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">СОЗДАНИЕ ГРУППЫ ПОЛЬЗОВАТЕЛЕЙ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/roles', 'data-abide', 'novalidate']) }}
    @include('roles.form', ['submitButtonText' => 'Создать', 'param' => ''])
  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.inputs-mask')
  @endpush

