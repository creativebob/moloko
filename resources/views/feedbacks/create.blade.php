@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новый отзыв')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОГО ОТЗЫВА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/feedbacks', 'data-abide', 'novalidate', 'class' => '', 'files' => true]) }}
    @include('feedbacks.form', ['submitButtonText' => 'Добавить отзыв', 'param' => ''])
  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
  @include('feedbacks.scripts')
  @endpush



