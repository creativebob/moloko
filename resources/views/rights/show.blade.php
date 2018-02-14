@extends('layouts.app')

@section('title', 'Просмотр правила доступа')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР ПРАВИЛА ДОСТУПА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($right, ['route' => ['rights.update', $right->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('rights.form', ['submitButtonText' => 'Редактировать правило', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection



