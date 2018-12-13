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

{{ Form::model($goods_product, ['route' => ['goods_products.update', $goods_product->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}
@include('goods_products.form', ['submit_text' => 'Редактировать'])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@endsection