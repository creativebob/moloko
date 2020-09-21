@extends('layouts.app')

@section('title', 'Редактировать этап')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $stage->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ этап</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($stage, ['url' => '/admin/stages/'.$stage->id, 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

@include('stages.form', ['submitButtonText' => 'Редактировать этап', 'param'=>''])

{{ Form::close() }}

@endsection

@section('modals')
@include('includes.modals.modal-rule-delete')
@endsection

@push('scripts')
@include('includes.scripts.modal-rule-delete-script')
@include('includes.scripts.inputs-mask')
@include('stages.scripts')
@endpush


