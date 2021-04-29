@extends('layouts.app')

@section('title', 'Новый поток')

@section('breadcrumbs', Breadcrumbs::render('create', $pageInfo))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">СОЗДАНИЕ НОВОГО потока</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::open(['route' => "{$pageInfo->alias}.store", 'data-abide', 'novalidate']) !!}
    @include('system.common.flows.form')
    {!! Form::close() !!}
@endsection



