@extends('layouts.app')

@section('title', 'Редактировать помещение')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $place->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ПОМЕЩЕНИЕ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($place, ['url' => '/admin/places/'.$place->id, 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
  {{ method_field('PATCH') }}
    @include('places.form', ['submitButtonText' => 'Редактировать помещение', 'param'=>''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
@endsection


