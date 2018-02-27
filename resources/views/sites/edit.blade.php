@extends('layouts.app')

@section('title', 'Редактировать сайт')

@section('breadcrumbs', Breadcrumbs::render('site-edit', $page_info, $site))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ сайт</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($site, ['route' => ['sites.update', $site->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('sites.form', ['submitButtonText' => 'Редактировать сайт', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
@endsection


