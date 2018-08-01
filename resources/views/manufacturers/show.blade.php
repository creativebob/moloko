@extends('layouts.app')

@section('title', 'Просмотр производителя')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР ПРОИЗВОДИТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($manufacturer, ['route' => ['manufacturers.update', $manufacturer->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('manufacturers.form', ['submitButtonText' => 'Редактировать производителя', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection



