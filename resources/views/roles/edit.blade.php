@extends('layouts.app')
@include('roles.inhead')

@section('title', 'Редактировать группу пользователей')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ГРУППУ ПОЛЬЗОВАТЕЛЕЙ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($role, ['route' => ['roles.update', $role->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('roles.form', ['submitButtonText' => 'Редактировать группу', 'param'=>''])
    
  {{ Form::close() }}

@endsection
@include('roles.scripts')


