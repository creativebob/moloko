@extends('layouts.app')

@section('inhead')
@include('includes.scripts.class.city_search')
@endsection

@section('title', 'Редактировать дилера')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $dealer->company->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ДИЛЕРА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($dealer, ['url' => '/admin/dealers/'.$dealer->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('dealers.form', ['submitButtonText' => 'Редактировать дилера', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection


