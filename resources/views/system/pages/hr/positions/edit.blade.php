@extends('layouts.app')

@section('title', 'Редактировать должность')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $position->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ должность</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($position, ['url' => '/admin/positions/'.$position->id, 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('system.pages.hr.positions.form', ['submitButtonText' => 'Редактировать должность', 'param'=>''])

  {{ Form::close() }}

@endsection

@push('scripts')
  @include('includes.scripts.inputs-mask')
  @include('system.pages.hr.positions.scripts')
@endpush


