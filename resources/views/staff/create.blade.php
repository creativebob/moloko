@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новая должность')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ должности</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'positions.store', 'data-abide', 'novalidate']) }}
    @include('positions.form', ['submitButtonText' => 'Добавить должность', 'param' => ''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
@endsection







