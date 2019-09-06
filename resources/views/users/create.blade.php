@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Новый пользователь')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
  <div class="top-bar-left">
   <h2 class="header-content">СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ</h2>
 </div>
 <div class="top-bar-right">
 </div>
</div>
@endsection

@section('content')

{{ Form::open(['url' => '/admin/users', 'data-abide', 'novalidate', 'files'=>'true']) }}
@include('users.form', ['submitButtonText' => 'Добавить пользователя', 'param' => ''])
{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@include('includes.scripts.upload-file')
@include('includes.scripts.delete-from-page-script')
@include('users.scripts')
@endsection



