@extends('layouts.app')

@section('title', 'Редактировать рассылку')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $mailing->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ рассылку &laquo{{ $mailing->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($mailing, ['route' => ['mailings.update', $mailing->id], 'data-abide', 'novalidate']) }}
        @method('PATCH')
        @include('system.pages.marketings.mailings.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
