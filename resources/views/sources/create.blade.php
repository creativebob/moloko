@extends('layouts.app')

@section('title', 'Новая должность')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ нового этапа</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/stages', 'data-abide', 'novalidate']) }}
    @include('stages.form', ['submitButtonText' => 'Добавить этап', 'param' => ''])
  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.inputs-mask')
  @include('stages.scripts')
  @endpush






