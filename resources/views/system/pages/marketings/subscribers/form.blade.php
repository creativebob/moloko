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

                            <div class="small-12 medium-4 cell">
                                <label>Имя
                                    @include('includes.inputs.name')
                                </label>
                            </div>

                            <div class="small-12 medium-4 cell">
                                <label>Email
                                    @include('includes.inputs.email', ['name' => 'email', 'required' => true, 'disabled' => isset($subscriber->subsctiberable) ? true : null])
                                </label>
                            </div>

                            <div class="small-12 medium-4 cell">
                                <label>Рассылка с сайта
                                    @include('includes.selects.sites')
                                </label>
                            </div>

                            <div class="cell small-12 checkbox">
                                {!! Form::hidden('is_active', 0) !!}
                                {!! Form::checkbox('is_active', 1, $subscriber->exists ? $subscriber->is_active : 1, ['id' => 'checkbox-is_active']) !!}
                                <label for="checkbox-is_active"><span>Действующий адрес</span></label>
                            </div>

                            <div class="cell small-12 checkbox">
                                {!! Form::hidden('deny', 0) !!}
                                {!! Form::checkbox('deny', 1, $subscriber->denied_at, ['id' => 'checkbox-deny']) !!}
                                <label for="checkbox-deny"><span>Запрет</span></label>
                            </div>
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
                @include('includes.control.checkboxes', ['item' => $subscriber])

                <div
                    class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitText, ['class' => 'button']) }}
                </div>
            </div>

        </div>
    </div>
</div>
