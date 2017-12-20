@extends('layouts.app')
@include('entities.inhead')

@section('title', 'Новая сущность')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕГИСТРАЦИЯ НОВОЙ СУЩНОСТИ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'entities.store', 'data-abide', 'novalidate']) }}
    @include('entities.form', ['submitButtonText' => 'Зарегистрировать', 'param' => ''])
  {{ Form::close() }}

@endsection
@include('entities.scripts')



