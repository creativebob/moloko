@extends('layouts.app')

@section('title', 'Редактировать скидку')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $campaign->name))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">Редактировать рекламную кампанияю &laquo{{ $campaign->name }}&raquo</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')
    {{ Form::model($campaign, ['route' => ['campaigns.update', $campaign->id], 'data-abide', 'novalidate']) }}
        @method('PATCH')
        @include('system.pages.marketings.campaigns.form', ['submitText' => 'Редактировать'])
    {{ Form::close() }}
@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')
{{--    @include('includes.scripts.check', ['entity' => 'campaigns', 'id' => $campaign->id])--}}
@endpush
