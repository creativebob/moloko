@extends('layouts.app')
@include('companies.inhead')

@section('title', 'Редактировать страницу')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ страницу "{{ $page->page_name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($page, ['route' => ['pages.update', $page->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('pages.form', ['submitButtonText' => 'Редактировать страницу', 'param'=>''])
    
  {{ Form::close() }}

@endsection


