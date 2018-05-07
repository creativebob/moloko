@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.inhead-pickmeup')
@endsection

@section('title', 'Просмотр пользователя')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($user, ['route' => ['users.update', $user->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('users.form', ['submitButtonText' => 'Редактировать пользователя', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection


