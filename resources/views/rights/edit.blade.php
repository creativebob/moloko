@extends('layouts.app')
@include('rights.inhead')

@section('title', 'Редактировать пользователя')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($right, ['route' => ['rights.update', $right->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('rights.form', ['submitButtonText' => 'Редактировать пользователя', 'param'=>''])
    
  {{ Form::close() }}

@endsection
@include('rights.scripts')


