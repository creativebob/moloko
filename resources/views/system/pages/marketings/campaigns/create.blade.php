@extends('layouts.app')

@section('title', 'Новая рекламная кампания')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Создание новой рекламной кампании</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::open(['route' => 'campaigns.store', 'data-abide', 'novalidate']) }}
        @include('system.pages.marketings.campaigns.form', ['submitText' => 'Добавить'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
{{--    @include('includes.scripts.check', ['entity' => 'campaigns'])--}}
@endpush
