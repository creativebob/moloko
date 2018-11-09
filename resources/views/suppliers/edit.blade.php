@extends('layouts.app')

@section('inhead')
@include('includes.scripts.class.city_search')
@endsection

@section('title', 'Редактировать поставщика')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $supplier->company->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ПОСТАВЩИКА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($supplier, ['url' => '/admin/suppliers/'.$supplier->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('suppliers.form', ['submitButtonText' => 'Редактировать поставщика', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection


