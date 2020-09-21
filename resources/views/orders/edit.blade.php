@extends('layouts.app')

@section('title', 'Редактировать сущность')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $entity->entity_name))

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

  {{ Form::model($entity, ['url' => '/admin/entities/'.$entity->id, 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('entities.form', ['submitButtonText' => 'Редактировать сущность', 'param'=>''])

  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.inputs-mask')
  @endpush


