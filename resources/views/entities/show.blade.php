@extends('layouts.app')

@section('title', 'Просмотр сущности')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР СУЩНОСТИ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($entity, ['route' => ['entities.update', $entity->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('entities.form', ['submit_text' => 'Редактировать сущность', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection



