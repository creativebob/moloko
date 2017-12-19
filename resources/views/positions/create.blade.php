@extends('layouts.app')

@section('inhead')
  <link rel="stylesheet" href="/js/pickmeup/css/pickmeup.css">
  <script type="text/javascript" src="/js/pickmeup/js/jquery.js"></script>
  <script type="text/javascript" src="/js/pickmeup/js/jquery.pickmeup.js"></script>
  <script type="text/javascript" src="/js/pickmeup/js/demo.js"></script>
@endsection

@section('title', 'Новая страница')

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

  {{ Form::open(['route' => 'positions.store', 'data-abide', 'novalidate']) }}
    @include('positions.form', ['submitButtonText' => 'Добавить должность', 'param' => ''])
  {{ Form::close() }}

@endsection






