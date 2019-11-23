@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать продвижение')

@section('breadcrumbs', Breadcrumbs::render('alias-edit', $page_info, $promotion))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ сайт</h2>
   </div>
   <div class="top-bar-right">
   </div>
</div>
@endsection

@section('content')

{{ Form::model($promotion, ['route' => ['promotions.update', $promotion->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@include('promotions.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}



@endsection

@section('modals')
    @include('includes.modals.modal-delete-ajax')
    <div id="modal"></div>
@endsection
