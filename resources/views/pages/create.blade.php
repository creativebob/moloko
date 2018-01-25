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

@section('breadcrumbs')
<div class="grid-x breadcrumbs">
  <div class="small-12 cell"> 
    <ul>
      <li><a href="/sites">Сайты</a></li>
      <li><a href="/pages?site_id={{ $current_site->id }}">{{ $current_site->site_name }}</a></li>
      <li><a href="/pages?site_id={{ $current_site->id }}">Страницы</a></li>
      <li>Новая страница</li>
    </ul>
  </div>
</div>
@endsection

@section('content')
  {{ Form::open(['route' => 'pages.store', 'data-abide', 'novalidate']) }}
    @include('pages.form', ['submitButtonText' => 'Добавить страницу', 'param' => ''])
  {{ Form::close() }}
@endsection






