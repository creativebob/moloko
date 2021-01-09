@extends('layouts.app')

@section('title', 'Новая метка заказа')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">Новая метка заказа</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::open(['route' => 'labels.store', 'data-abide', 'novalidate']) !!}
    @include('system.pages.labels.form', ['submitText' => 'Редактировать'])
    {!! Form::close() !!}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
