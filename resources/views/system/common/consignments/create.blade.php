@extends('layouts.app')

@section('inhead')
	@include('includes.scripts.pickmeup-inhead')
	@include('includes.scripts.class.digitfield')
@endsection

@section('title', 'Новая товарная накладная')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Новая товарная накладная</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::open(['route' => 'consignments.store', 'data-abide', 'novalidate']) }}

@include('consignments.form', ['submit_text' => 'Принять'])

{{ Form::close() }}

@endsection

@section('scripts')
	@include('includes.scripts.inputs-mask')
	@include('includes.scripts.pickmeup-script')
@endsection



