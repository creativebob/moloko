@extends('layouts.app')

@section('title', 'Новый дополнительный реквизит')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОГО РЕКВИЗИТА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/extra_requisites', 'data-abide', 'novalidate', 'class' => '']) }}
    @include('extra_requisites.form', ['submitButtonText' => 'Добавить реквизит', 'param' => ''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  
@endsection



