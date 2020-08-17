@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
@endsection

@section('title', 'Редактировать выполненную работу')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $outcome->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ выполненную работу</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($outcome, ['route' => ['outcomes.update', $outcome->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
        @method('PATCH')
        @include('system.pages.outcomes.form', ['submit_text' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.check', ['entity' => 'outcomes', 'id' => $outcome->id])
@endpush
