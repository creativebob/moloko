@extends('layouts.app')

@section('inhead')
@include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать товарную накладную')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $consignment->number))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ товарную накладную</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($consignment, ['route' => ['consignments.update', $consignment->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@include('consignments.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@section('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@endsection


