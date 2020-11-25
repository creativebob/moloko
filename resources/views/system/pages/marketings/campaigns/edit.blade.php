@extends('layouts.app')

@section('title', 'Редактировать скидку')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $discount->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ скидку &laquo{{ $discount->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($discount, ['route' => ['discounts.update', $discount->id], 'data-abide', 'novalidate']) }}
        @method('PATCH')
        @include('system.pages.marketings.discounts.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
{{--    @include('includes.scripts.check', ['entity' => 'discounts', 'id' => $discount->id])--}}
@endpush
