@extends('layouts.app')

@section('title', 'Редактировать группу артикулов')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $processesGroup->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАНИЕ ГРУППЫ артикулов</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($processesGroup, ['route' => ['processes_groups.update', $processesGroup->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}
@include('products.processes_groups.form', ['submit_text' => 'Редактировать'])
{{ Form::close() }}

@endsection
