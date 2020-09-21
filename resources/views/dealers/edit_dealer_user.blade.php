@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.pickmeup-inhead')
    @include('includes.scripts.class.city_search')
@endsection

@section('title', 'Редактировать дилера')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $dealer->client->clientable->first_name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ ДИЛЕРА</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($dealer->client->clientable, ['url' => '/admin/dealers/update-user/'.$dealer->id, 'data-abide', 'novalidate', 'class' => 'form-check-city', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
        @include('system.pages.marketings.users.form', ['submitButtonText' => 'Редактировать', 'param'=>'', 'user'=>$dealer->client->clientable])
    {{ Form::close() }}
@endsection

@section('modals')
    {{-- Модалка добавления роли --}}
    @include('includes.modals.modal-add-role')

    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection

@push('scripts')
    @include('system.pages.marketings.users.scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.delete-from-page-script')
    @include('includes.scripts.upload-file')
    @include('includes.scripts.extra-phone')
    @endpush


