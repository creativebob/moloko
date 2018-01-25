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

@section('breadcrumbs')
<div class="grid-x breadcrumbs">
  <div class="small-12 cell"> 
    <ul>
      <li><a href="/sites">Сайты</a></li>
      <li><a href="/pages?site_id={{ $page->site->id }}">{{ $page->site->site_name }}</a></li>
      <li><a href="/pages?site_id={{ $page->site->id }}">Страницы</a></li>
      <li>Страница "{{ $page->page_name }}"</li>
    </ul>
  </div>
</div>
@endsection

@section('content')

  {{ Form::model($page, ['route' => ['pages.update', $page->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('pages.form', ['submitButtonText' => 'Редактировать страницу', 'param'=>''])
    
  {{ Form::close() }}

@endsection


