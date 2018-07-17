@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать сотрудника')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $staffer->position->name))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">{{ $staffer->position->position_name }}</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($staffer, ['url' => '/admin/staff/'.$staffer->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('staff.form', ['submitButtonText' => 'Редактировать сотрудника', 'param'=>''])
    
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
@endsection


