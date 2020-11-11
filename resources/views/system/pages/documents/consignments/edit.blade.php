@extends('layouts.app')

@section('inhead')
    @include('includes.scripts.class.digitfield')
@endsection

@section('title', 'Редактировать товарную накладную')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $consignment->id))

@section('title-content')
    <div class="top-bar head-content">
        <div class="top-bar-left">
            <h2 class="header-content">РЕДАКТИРОВАТЬ товарную накладную</h2>
        </div>
        <div class="top-bar-right">
            @if($consignment->conducted_at && extra_right('consignment-cancel'))
                <a href="{{ route('consignments.cancel', $consignment->id) }}" class="button">Отменить оприходование</a>
            @endif
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
                                @include('includes.inputs.digit', ['name' => 'number',  'required' => true, 'value' => $consignment->id])
                            </label>
                        </div>

                        <div class="small-6 medium-3 cell">
                            <label>Дата
                                <pickmeup-component
                                    value="{{ $consignment->date }}"
                                ></pickmeup-component>
                            </label>
                        </div>

                        <div class="small-12 medium-6 cell">
                            <label>Поставщик
                                @include('includes.selects.suppliers', ['supplier_id' => $consignment->supplier_id ?? null])
                            </label>
                        </div>

                        <div class="small-12 medium-6 cell">
                            <label>Склад
                                @include('includes.selects.stocks', ['stock_id' => $consignment->stock_id])
                            </label>
                        </div>

                        {{--                    <div class="small-12 medium-6 cell">--}}
                        {{--                        <label>Сумма--}}
                        {{--                            <input-digit-component name="amount" rate="2" :value="{{ $consignment->amount ?? 0 }}"></input-digit-component>--}}
                        {{--                             @include('includes.inputs.digit',--}}
                        {{--                                [--}}
                        {{--                                'name' => 'amount',--}}
                        {{--                                'value'=>$consignment->amount,--}}
                        {{--                                'decimal_place'=> 2,--}}
                        {{--                                'required' => true--}}
                        {{--                            ]) --}}
                        {{--                        </label>--}}
                        {{--                    </div>--}}

                        {{--                    <div class="small-12 cell checkbox">--}}
                        {{--                        {!! Form::hidden('draft', 0) !!}--}}
                        {{--                        {!! Form::checkbox('draft', 1, null, ['id' => 'draft']) !!}--}}
                        {{--                        <label for="draft"><span>Черновик</span></label>--}}
                        {{--                    </div>--}}

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

        <consignment-component
            :consignment='@json($consignment)'
            :select-data='@json($articles_categories_with_items_data)'

            @if (auth()->user()->company->currencies->isNotEmpty())
            :currencies='@json(auth()->user()->company->currencies)'
            @endif

        ></consignment-component>

        @empty($consignment->conducted_at)
            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit('Редактировать', ['class' => 'button']) }}
            </div>

            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit('Оприходовать', ['class' => 'button', 'id' => 'button-conducting']) }}
            </div>
        @endempty

    </div>

    {{ Form::close() }}

@endsection

@push('scripts')
    @include('includes.scripts.inputs-mask')

    <script>
        $(document).on('click', '#button-conducting', function () {
            let id = '{{ $consignment->id }}';
            $(this).closest('form').attr('action', '/admin/consignments/' + id + '/conducting');
        })

    </script>
@endpush


