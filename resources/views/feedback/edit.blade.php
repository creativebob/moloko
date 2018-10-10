@extends('layouts.app')

@section('title', 'Редактировать отзыв')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $feedback->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ОТЗЫВ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($feedback, ['url' => '/admin/feedback/'.$feedback->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('feedback.form', ['submitButtonText' => 'Редактировать отзыв', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


