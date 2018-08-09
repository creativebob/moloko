@extends('layouts.app')

@section('title', 'Редактировать группу товаров')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $goods_product->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАНИЕ ГРУППЫ ТОВАРОВ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($goods_product, ['url' => '/admin/goods_products/'.$goods_product->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('goods_products.form', ['submitButtonText' => 'Редактировать группу товаров', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


