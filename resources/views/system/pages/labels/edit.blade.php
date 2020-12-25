@extends('layouts.app')

@section('title', 'Редактировать метку заказа')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $label->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ метку заказа</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($label, ['route' => ['labels.update', $label->id], 'data-abide', 'novalidate']) }}
        @method('PATCH')
        @include('system.pages.labels.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
