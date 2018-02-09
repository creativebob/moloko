@extends('layouts.app')

@section('inhead')
  @include('includes.inhead-pickmeup')
@endsection

@section('title', 'Редактировать сотрудника')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ сотрудника</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($staffer, ['route' => ['staff.update', $staffer->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('staff.form', ['submitButtonText' => 'Редактировать сотрудника', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.inputs-mask')
@endsection


