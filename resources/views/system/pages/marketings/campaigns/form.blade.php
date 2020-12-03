@php
    $disabled = $campaign->id ? 'true' : 'false';
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
                                <label>Название рекламной кампании
                                    @include('includes.inputs.name', ['required' => true, 'check' => true])
                                    <div class="sprite-input-right find-status" id="alias-check"></div>
                                    <div class="item-error">Такая кампания уже существует!</div>
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Филиал
                                    @include('includes.selects.user_filials')
                                </label>
                            </div>

                            {{-- <div class="cell small-12 medium-6">
                                <label>Назначение
                                    {!! Form::select('entity_id', $entities->pluck('name', 'id'), $campaign->entity_id, ['required', ($disabled == 'true') ? 'disabled' : '']) !!}
                                </label>
                            </div> --}}

                            <div class="small-3 medium-6 large-3 cell">
                                <label>Дата начала
                                    <pickmeup-component
                                        name="begin_date"
                                        value="{{ isset($campaign->begined_at) ? $campaign->begined_at->format('Y-m-d') : null }}"
                                        :required="true"
                                        @if($campaign->id)
                                            :disabled="true"
                                        @endif
                                    ></pickmeup-component>
                                </label>
                            </div>
                            <div class="small-3 medium-6 large-3 cell">
                                <label>Время начала:
                                    @include('includes.inputs.time', ['name' => 'begin_time', 'placeholder' => true, 'value' => isset($campaign->begined_at) ? $campaign->begined_at->format('H:i') : null, 'required' => null, 'disabled' => $campaign->id ? true : null])
                                </label>
                            </div>

                            <div class="small-3 medium-6 large-3 cell">
                                <label>Дата окончания
                                    <pickmeup-component
                                        name="end_date"
                                        value="{{ isset($campaign->ended_at) ? $campaign->ended_at->format('Y-m-d') : null }}"
                                        @if($campaign->id)
                                            :disabled="true"
                                        @endif
                                    ></pickmeup-component>
                                </label>
                            </div>
                            <div class="small-3 medium-6 large-3 cell">
                                <label>Время окончания:
                                    @include('includes.inputs.time', ['name' => 'end_time', 'placeholder' => true, 'value' => isset($campaign->ended_at) ? $campaign->ended_at->format('H:i') : null, 'disabled' => $campaign->id ? true : null])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Описание
                                    @include('includes.inputs.textarea', ['name' => 'description'])
                                </label>
                            </div>

                            {{-- {!! Form::hidden('is_block', ($disabled == 'true') ? $campaign->is_block : 0 ) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_block', 1, $campaign->is_block, ['id' => 'checkbox-is_block', ($disabled == 'true') ? 'disabled' : '']) !!}
                                <label for="checkbox-is_block"><span>Запрет на наложение последующих скидок</span></label>
                            </div> --}}
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
                @include('includes.control.checkboxes', ['item' => $campaign])

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitText, ['class' => 'button']) }}
                </div>
            </div>

        </div>
    </div>
</div>
