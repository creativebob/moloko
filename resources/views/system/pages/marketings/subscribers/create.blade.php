@extends('layouts.app')

@section('title', 'Новый подписчик')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">СОЗДАНИЕ Нового подписчика</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::open(['route' => 'subscribers.store', 'data-abide', 'novalidate']) }}
        @include('system.pages.marketings.subscribers.form', ['submitText' => 'Добавить'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
