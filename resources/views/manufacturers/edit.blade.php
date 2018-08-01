@extends('layouts.app')

@section('title', 'Редактировать производителя')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $manufacturer->company->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ПРОИЗВОДИТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($manufacturer, ['url' => '/admin/manufacturers/'.$manufacturer->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('manufacturers.form', ['submitButtonText' => 'Редактировать производителя', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


