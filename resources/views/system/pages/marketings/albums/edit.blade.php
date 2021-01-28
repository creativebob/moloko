@extends('layouts.app')

@section('title', 'Редактировать альбом')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $album->name))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ альбом</h2>
        </div>
        <div class="top-bar-right">
        </div>
    </div>
@endsection

@section('content')
    {!! Form::model($album, ['route' => ['albums.update', $album->id], 'data-abide', 'novalidate']) !!}
    @method('PATCH')
    @include('system.pages.marketings.albums.form', ['submitText' => 'Редактировать'])
    {!! Form::close() !!}
@endsection
