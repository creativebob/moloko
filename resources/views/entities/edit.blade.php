@extends('layouts.app')
@include('entities.inhead')

@section('title', 'Редактировать сущность')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ СУЩНОСТЬ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($entity, ['route' => ['entities.update', $entity->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('entities.form', ['submitButtonText' => 'Редактировать сущность', 'param'=>''])
    
  {{ Form::close() }}

@endsection
@include('entities.scripts')


