@extends('layouts.app')

@section('title', 'Редактировать пользователя')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $right->right_name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($right, ['url' => '/admin/rights/'.$right->id, 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('rights.form', ['submitButtonText' => 'Редактировать пользователя', 'param'=>''])

  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection
