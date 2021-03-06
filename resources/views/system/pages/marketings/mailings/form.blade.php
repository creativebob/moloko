@php
    $disabled = isset($mailing->begined_at) ? true : null;
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
                                <label>Заголовок
                                    @include('includes.inputs.name', ['name' => 'subject'])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>От кого (имя)
                                    @include('includes.inputs.name', ['name' => 'from_name', 'value' => config('mail.from.name')] )
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>От кого (email)
                                    @include('includes.inputs.email', ['name' => 'from_email', 'value' => config('mail.from.address')])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Шаблон
                                    @include('includes.selects.templates', ['categoryId' => 1])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Список рассылки
                                    @include('includes.selects.mailing_lists', ['placeholder' => 'Рассылка в ручную'])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Дата автоматического запуска
                                    <pickmeup-component
                                        name="started_at"
                                        @isset($disabled)
                                        :readonly="true"
                                        @endisset
                                        :required="true"
                                    ></pickmeup-component>
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Описание
                                    @include('includes.inputs.textarea', ['name' => 'description'])
                                </label>
                            </div>

                            @empty($mailing->ended_at)
                                <div class="small-12 medium-6 cell">
                                    {!! Form::hidden('is_active', 0) !!}
                                    <div class="cell small-12 checkbox">
                                        {!! Form::checkbox('is_active', 1, $mailing->is_active, ['id' => 'checkbox-is_active']) !!}
                                        <label for="checkbox-is_active"><span>Активировать</span></label>
                                    </div>
                                </div>
                            @endempty
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
                @include('includes.control.checkboxes', ['item' => $mailing])

                <div
                    class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitText, ['class' => 'button']) }}
                </div>
            </div>

        </div>
    </div>
</div>
