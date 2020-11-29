@extends('layouts.app')

@section('title', 'Редактировать хранилище')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $stock->cmv->article->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ хранилище "{{ $stock->cmv->article->name }}"</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {{ Form::model($stock, ['route' => [$stock->getTable() . '.update', $stock->id], 'data-abide', 'novalidate']) }}
    {{ method_field('PATCH') }}
    @include('system.common.stocks.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
    {{--@include('stocks.scripts')--}}
    {{-- Проверка поля на существование --}}
    {{--@include('includes.scripts.check', ['entity' => 'stocks'])--}}
@endpush
