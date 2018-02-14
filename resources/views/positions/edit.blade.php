@extends('layouts.app')

@section('title', 'Редактировать должность')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ должность</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($position, ['route' => ['positions.update', $position->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('positions.form', ['submitButtonText' => 'Редактировать должность', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection


