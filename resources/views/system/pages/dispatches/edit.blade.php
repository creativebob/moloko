@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать рассылку')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $pageInfo, $dispatch))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ рассылку</h2>
   </div>
   <div class="top-bar-right">
   </div>
</div>
@endsection

@section('content')

{{ Form::model($dispatch, ['route' => ['dispatches.update', $dispatch->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('system.pages.dispatches.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}



@endsection

@section('modals')
    @include('includes.modals.modal-delete-ajax')
    <div id="modal"></div>
@endsection
