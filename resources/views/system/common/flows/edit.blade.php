@extends('layouts.app')

@section('title', 'Редактировать поток')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $flow->process->process->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">Поток: "{{ $flow->process->process->name }}"</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($flow, ['route' => ["{$pageInfo->alias}.update", $flow->id], 'data-abide', 'novalidate']) !!}
    {{ method_field('PATCH') }}
    @include('system.common.flows.form')
    {!! Form::close() !!}
@endsection


