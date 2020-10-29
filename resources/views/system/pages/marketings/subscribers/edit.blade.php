@extends('layouts.app')

@section('title', 'Редактировать подписчика')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $subscriber->email))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ подписчика &laquo{{ $subscriber->email }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($subscriber, ['route' => ['subscribers.update', $subscriber->id], 'data-abide', 'novalidate']) }}
        @method('PATCH')
        @include('system.pages.marketings.subscribers.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
