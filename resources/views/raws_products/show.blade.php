@extends('layouts.app')

@section('title', 'Просмотр сырья')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ПРОСМОТР ГРУППЫ СЫРЬЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($raws_product, ['route' => ['companies.update', $raws_product->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}
    @include('companies.form', ['submitButtonText' => 'Редактировать группу сырья', 'param' => 'readonly'])
  {{ Form::close() }}

@endsection



