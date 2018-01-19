@extends('layouts.app')

@section('title', 'Новая группа пользователей')

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

  {{ Form::open(['route' => 'roles.store', 'data-abide', 'novalidate']) }}
    @include('roles.form', ['submitButtonText' => 'Создать', 'param' => ''])
  {{ Form::close() }}

@endsection
@include('roles.scripts')



