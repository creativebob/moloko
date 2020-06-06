@extends('layouts.app')

@section('title', 'Редактировать продвижение')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $promotion))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ продвижение</h2>
   </div>
   <div class="top-bar-right">
   </div>
</div>
@endsection

@section('content')

{{ Form::model($promotion, ['route' => ['promotions.update', $promotion->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
{{ method_field('PATCH') }}

@include('system.pages.promotions.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}



@endsection

@section('modals')
    @include('includes.modals.modal-delete-ajax')
    <div id="modal"></div>
@endsection
