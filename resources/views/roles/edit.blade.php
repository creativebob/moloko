@extends('layouts.app')

@section('title', 'Редактировать группу пользователей')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $role->name))

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

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection
