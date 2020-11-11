@extends('layouts.app')

@section('title', 'Редактировать торговую точку')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $outlet->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ торговую точку</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($outlet, ['route' => ['outlets.update', $outlet->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
        @method('PATCH')
        @include('system.pages.outlets.form', ['submit_text' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
