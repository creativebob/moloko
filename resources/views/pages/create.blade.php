@extends('layouts.app')

@section('title', 'Новая страница')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ страницы</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'pages.store', 'data-abide', 'novalidate']) }}
    @include('pages.form', ['submitButtonText' => 'Добавить страницу', 'param' => ''])
  {{ Form::close() }}

@endsection






