@extends('layouts.app')

@section('title', 'Редактировать рабочее место')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $workplace->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ рабочее место</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($workplace, ['route' => ['workplaces.update', $workplace->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
        @method('PATCH')
        @include('system.pages.workplaces.form', ['submit_text' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
@endpush
