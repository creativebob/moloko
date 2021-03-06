@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать отзыв')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $feedback->person))

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

  {{ Form::model($feedback, ['url' => '/admin/feedbacks/'.$feedback->id, 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => true]) }}
  {{ method_field('PATCH') }}
    @include('feedbacks.form', ['submitButtonText' => 'Редактировать отзыв', 'param'=>''])
  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
  @include('feedbacks.scripts')
  @endpush


