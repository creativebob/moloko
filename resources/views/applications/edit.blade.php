@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать заявку')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $application->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ЗАЯВКУ ПОСТАВЩИКА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($application, ['url' => '/admin/applications/'.$application->id, 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('applications.form', ['submitButtonText' => 'Редактировать', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
    @include('includes.scripts.ckeditor')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.upload-file')
@endsection


