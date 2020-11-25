@extends('layouts.app')

@section('title', 'Новая скидка')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ НОВОй скидки</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::open(['route' => 'discounts.store', 'data-abide', 'novalidate']) }}
        @include('system.pages.marketings.discounts.form', ['submitText' => 'Добавить'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
{{--    @include('includes.scripts.check', ['entity' => 'discounts'])--}}
@endpush
