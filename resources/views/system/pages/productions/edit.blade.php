@extends('layouts.app')

@section('inhead')
	@include('includes.scripts.pickmeup-inhead')
	@include('includes.scripts.class.digitfield')
@endsection

@section('title', 'Редактировать наряд на производство')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $production->id))

@section('title-content')
<div class="top-bar head-content">
    <div class="top-bar-left">
        <h2 class="header-content">РЕДАКТИРОВАТЬ наряд на производство</h2>
    </div>
    <div class="top-bar-right">
        @if(Auth::user()->god && $production->is_posted)
            <a href="{{ route('productions.unpost', $production->id) }}" class="button">Отменить проводку</a>
            @endif
    </div>
</div>
@endsection

@section('content')

{{ Form::model($production, ['route' => ['productions.update', $production->id], 'data-abide', 'novalidate']) }}
{{ method_field('PATCH') }}

<div class="grid-x tabs-wrap inputs">

    <div class="small-12 cell tabs-margin-top">
        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-12 large-6 cell">
                <div class="grid-x grid-padding-x">

                    <div class="small-6 medium-3 cell">
                        <label>Номер
                            @include('includes.inputs.digit', ['name' => 'number',  'required' => true, 'value' => $production->id])
                        </label>
                    </div>

                    <div class="small-6 medium-3 cell">
                        <label>Дата
                            @include('includes.inputs.date', ['name' => 'receipt_date', 'value' => $production->receipt_date->format('d.m.Y')])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Поставщик
                            @include('includes.selects.suppliers', ['supplier_id' => $production->supplier_id ?? null])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Склад
                            @include('includes.selects.stocks', ['stock_id' => $production->stock_id])
                        </label>
                    </div>

{{--                    <div class="small-12 medium-6 cell">--}}
{{--                        <label>Сумма--}}
{{--                            <input-digit-component name="amount" rate="2" :value="{{ $production->amount ?? 0 }}"></input-digit-component>--}}
{{--                             @include('includes.inputs.digit',--}}
{{--                                [--}}
{{--                                'name' => 'amount',--}}
{{--                                'value'=>$production->amount,--}}
{{--                                'decimal_place'=> 2,--}}
{{--                                'required' => true--}}
{{--                            ]) --}}
{{--                        </label>--}}
{{--                    </div>--}}

                    <div class="small-12 cell checkbox">
                        {!! Form::hidden('draft', 0) !!}
                        {!! Form::checkbox('draft', 1, null, ['id' => 'draft']) !!}
                        <label for="draft"><span>Черновик</span></label>
                    </div>

                    {{-- Чекбоксы управления --}}
                    {{-- @include('includes.control.checkboxes', ['item' => $production]) --}}

                </div>
            </div>

            <div class="small-12 medium-12 large-6 cell">
                <div class="grid-x grid-padding-x">


                    <div class="small-12 cell">
                        <label>Комментарий:
                            {{ Form::textarea('description', $production->description ?? null, []) }}
                        </label>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <production-component :production='@json($production)' :select-data='@json($articles_categories_with_items_data)'></production-component>

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit('Редактировать', ['class' => 'button']) }}
    </div>

    @if(!$production->is_posted)
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit('Произвести', ['class' => 'button', 'id' => 'button-produced']) }}
    </div>
        @endif

</div>

{{ Form::close() }}

@endsection

@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')

    <script>
        $(document).on('click', '#button-produced', function () {
            let id = '{{ $production->id }}';
            $(this).closest('form').attr('action', '/admin/productions/' + id + '/produced');
        })

    </script>
@endpush


