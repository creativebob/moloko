@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать сотрудника')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, 'Редактировать сотрудника'))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ сотрудника</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($employee->user, ['url' => '/admin/employees/'.$employee->id, 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('users.form', ['submitButtonText' => 'Редактировать сотрудника', 'param'=>'', 'user'=>$employee->user])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
@endsection


