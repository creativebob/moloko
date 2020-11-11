@extends('layouts.app')

@section('title', 'Просмотр агента')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР АГЕНТА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($agent, ['route' => ['agents.update', $agent->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('system.pages.sales.agents.form', ['submitButtonText' => 'Редактировать агента', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection



