@extends('layouts.app')
@include('staff.inhead')

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

@include('staff.scripts')


