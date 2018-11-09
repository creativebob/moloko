@extends('layouts.app')

@section('title', 'Редактировать реквизит')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $extra_requisite->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ДОПОЛНИТЕЛЬНЫЙ РЕКВИЗИТ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($extra_requisite, ['url' => '/admin/extra_requisites/'.$extra_requisite->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('extra_requisites.form', ['submitButtonText' => 'Редактировать реквизит', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


