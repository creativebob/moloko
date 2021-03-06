@extends('layouts.app')

@section('title', 'Новая должность')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ должности</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/positions', 'data-abide', 'novalidate']) }}
    @include('system.pages.hr.positions.form', ['submitButtonText' => 'Добавить должность', 'param' => ''])
  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.inputs-mask')
  @include('system.pages.hr.positions.scripts')
@endpush






