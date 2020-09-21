@extends('layouts.app')

@section('title', 'Редактировать клиента')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $client->clientable->name ?? 'Имя не указано'))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ КЛИЕНТА</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($user, ['route' => ['clients.updateClientUser', $client->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
        {{ method_field('PATCH') }}
        @include('system.pages.marketings.users.form', ['submitButtonText' => 'Редактировать'])
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
