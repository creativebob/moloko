@extends('layouts.app')

@section('inhead')
	@include('includes.scripts.pickmeup-inhead')
	@include('includes.scripts.class.digitfield')
@endsection

@section('title', 'Редактировать товарную накладную')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $consignment->id))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ товарную накладную</h2>
    </div>
    <div class="top-bar-right">
    </div>
</div>
@endsection

@section('content')

{{ Form::model($consignment, ['route' => ['consignments.update', $consignment->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

<div class="grid-x tabs-wrap inputs">

    <div class="small-12 cell tabs-margin-top">
        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-12 large-6 cell">
                <div class="grid-x grid-padding-x">

                    <div class="small-6 medium-3 cell">
                        <label>Номер
                            @include('includes.inputs.digit', ['name' => 'number',  'required' => true])
                        </label>
                    </div>

                    <div class="small-6 medium-3 cell">
                        <label>Дата
                            @include('includes.inputs.date', ['name' => 'receipt_date', 'value' => $consignment->receipt_date->format('d.m.Y')])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Поставщик
                            @include('includes.selects.suppliers', ['supplier_id' => $consignment->supplier_id ?? null])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Сумма
{{--                            <input-digit-component name="amount" rate="2" :value="{{ $consignment->amount ?? 0 }}"></input-digit-component>--}}
                            {{-- @include('includes.inputs.digit',
                                [
                                'name' => 'amount',
                                'value'=>$consignment->amount,
                                'decimal_place'=> 2,
                                'required' => true
                            ]) --}}
                        </label>
                    </div>

                    <div class="small-12 cell checkbox">
                        {!! Form::hidden('draft', 0) !!}
                        {!! Form::checkbox('draft', 1, null, ['id' => 'draft']) !!}
                        <label for="draft"><span>Черновик</span></label>
                    </div>

                    {{-- Чекбоксы управления --}}
                    {{-- @include('includes.control.checkboxes', ['item' => $consignment]) --}}

                </div>
            </div>

            <div class="small-12 medium-12 large-6 cell">
                <div class="grid-x grid-padding-x">


                    <div class="small-12 cell">
                        <label>Комментарий:
                            {{ Form::textarea('description', $consignment->description ?? null, []) }}
                        </label>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <consignment-component :consignment='@json($consignment)' :select-data='@json($articles_categories_with_items_data)'></consignment-component>

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit('Редактировать', ['class' => 'button']) }}
    </div>

</div>

{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@endpush


