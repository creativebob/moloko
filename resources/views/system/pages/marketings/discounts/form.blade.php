@php
    $disabled = $discount->id ? 'true' : 'false';
@endphp

<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">

            <li class="tabs-title is-active">
                <a href="#tab-options" aria-selected="true">Информация</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div data-tabs-content="tabs">

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-options">

                {{-- Разделитель на первой вкладке --}}
                <div class="grid-x grid-padding-x">

                    {{-- Левый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">

                        {{-- Основная инфа --}}
                        <div class="grid-x grid-margin-x">

                            <div class="small-12 medium-6 cell">
                                <label>Название
                                    @include('includes.inputs.name', ['required' => true, 'check' => true])
                                    <div class="sprite-input-right find-status" id="alias-check"></div>
                                    <div class="item-error">Такой альбом уже существует!</div>
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                            </div>

                            <div class="cell small-12 medium-6">
                                <discount-mode-component
                                    :discount="{{ $discount }}"
                                    :disabled="{{ (($disabled == 'true') ? 'true' : 'false') }}"
                                ></discount-mode-component>
                            </div>

                            <div class="cell small-12 medium-6">
                                <label>Назначение
                                    {!! Form::select('entity_id', $entities->pluck('name', 'id'), $discount->entity_id, ['required', ($disabled == 'true') ? 'disabled' : '']) !!}
                                </label>
                            </div>

                            <div class="small-3 medium-6 large-3 cell">
                                <pickmeup-component
                                    name="begin_date"
                                    title="Дата начала"
                                    value="{{ isset($discount->begined_at) ? $discount->begined_at->format('Y-m-d') : null }}"
                                    :required="true"
                                    @if($discount->id)
                                        :disabled="true"
                                    @endif
                                ></pickmeup-component>
                            </div>
                            <div class="small-3 medium-6 large-3 cell">
{{--                                    <input-time-component--}}
{{--                                        name="begin_time"--}}
{{--                                        value="{{ isset($discount->begined_at) ? $discount->begined_at->format('H:i') : null }}"--}}
{{--                                        :required="true"--}}
{{--                                    ></input-time-component>--}}
                                <label>Время начала:
                                    @include('includes.inputs.time', ['name' => 'begin_time', 'placeholder' => true, 'value' => isset($discount->begined_at) ? $discount->begined_at->format('H:i') : null, 'required' => null, 'disabled' => $discount->id ? true : null])
                                </label>
                            </div>

                            <div class="small-3 medium-6 large-3 cell">
                                <pickmeup-component
                                    name="end_date"
                                    title="Дата окончания"
                                    value="{{ isset($discount->ended_at) ? $discount->ended_at->format('Y-m-d') : null }}"
                                    @if($discount->id)
                                        :disabled="true"
                                    @endif
                                ></pickmeup-component>
                            </div>
                            <div class="small-3 medium-6 large-3 cell">
                                <label>Время окончания:
                                    @include('includes.inputs.time', ['name' => 'end_time', 'placeholder' => true, 'value' => isset($discount->ended_at) ? $discount->ended_at->format('H:i') : null, 'disabled' => $discount->id ? true : null])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Описание
                                    @include('includes.inputs.textarea', ['name' => 'description'])
                                </label>
                            </div>

{{--                            {!! Form::hidden('is_conditions', 0) !!}--}}
{{--                            <div class="small-12 cell checkbox">--}}
{{--                                {!! Form::checkbox('is_conditions', 1, $discount->is_conditions, ['id' => 'checkbox-is_conditions']) !!}--}}
{{--                                <label for="checkbox-is_conditions"><span>Включить условия</span></label>--}}
{{--                            </div>--}}


                            {!! Form::hidden('is_block', ($disabled == 'true') ? $discount->is_block : 0 ) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_block', 1, $discount->is_block, ['id' => 'checkbox-is_block', ($disabled == 'true') ? 'disabled' : '']) !!}
                                <label for="checkbox-is_block"><span>Запрет на наложение последующих скидок</span></label>
                            </div>

{{--                            <div class="small-6 cell">--}}
{{--                                <label>Начало--}}
{{--                                    @include('includes.inputs.date', [--}}
{{--                                        'name' => 'begin_date',--}}
{{--                                        'value' => isset($discount->begin_date) ? $discount->begin_date->format('d.m.Y') : today()->format('d.m.Y'),--}}
{{--                                        'required' => true--}}
{{--                                    ]--}}
{{--                                    )--}}
{{--                                </label>--}}
{{--                            </div>--}}
{{--                            <div class="small-6 cell">--}}
{{--                                <label>Окончание--}}
{{--                                    @include('includes.inputs.date', [--}}
{{--                                        'name' => 'end_date',--}}
{{--                                        'value' => isset($discount->end_date) ? $discount->end_date->format('d.m.Y') : ''--}}
{{--                                    ]--}}
{{--                                    )--}}
{{--                                </label>--}}
{{--                            </div>--}}



                        </div>

                    </div>

                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        <div class="grid-x">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-x grid-padding-x">
                {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $discount])

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitText, ['class' => 'button']) }}
                </div>
            </div>

        </div>
    </div>
</div>
