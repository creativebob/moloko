@extends('layouts.app')

@section('title', 'Редактировать склад')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $stock->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ склад</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($stock, ['route' => ['stocks.update', $stock->id], 'data-abide', 'novalidate']) }}
@method('PATCH')
@include('system.pages.stocks.form', ['submit_text' => 'Редактировать'])

{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('system.pages.stocks.scripts')
{{-- Проверка поля на существование --}}
@include('includes.scripts.check', ['entity' => 'stocks', 'id' => $stock->id])
@endpush
