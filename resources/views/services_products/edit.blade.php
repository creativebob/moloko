@extends('layouts.app')

@section('title', 'Редактировать группу услуг')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $services_product->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАНИЕ ГРУППЫ УСЛУГ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($services_product, ['url' => '/admin/services_products/'.$services_product->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('services_products.form', ['submitButtonText' => 'Редактировать группу услуг', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


