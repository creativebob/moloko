@extends('layouts.app')

@section('title', 'Редактировать страницу')

@section('breadcrumbs', Breadcrumbs::render('section-edit', $parent_page_info, $site, $page_info, $page))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ страницу "{{ $page->name }}"</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($page, ['url' => '/admin/sites/'.$site->alias.'/pages/'.$page->id, 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('pages.form', ['submitButtonText' => 'Редактировать страницу', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('pages.scripts')
@endsection


