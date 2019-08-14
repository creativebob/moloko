@extends('layouts.app')

@section('inhead')
@include('includes.scripts.class.city_search')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новый сотрудник')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
   <h2 class="header-content">СОЗДАНИЕ НОВОГО СОТРУДНИКА</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')

{{ Form::open(['url' => '/admin/employees', 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => 'true']) }}
@include('users.form', ['submitButtonText' => 'Добавить сотрудника', 'param' => ''])
{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')
@include('includes.scripts.delete-from-page-script')
@include('users.scripts')
@endpush



