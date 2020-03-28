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

                            <div class="small-12  medium-6 cell">
                                <label>Категория
                                    @include('includes.selects.categories', ['category_id' => $outcome->category_id, 'category_entity' => 'outcomes_categories'])
                                </label>
                            </div>

                            <div class="small-6 cell">
                                <label>Начало
                                    @include('includes.inputs.date', [
                                        'name' => 'begin_date',
                                        'value' => isset($outcome->begin_date) ? $outcome->begin_date->format('d.m.Y') : today()->format('d.m.Y'),
                                        'required' => true
                                    ]
                                    )
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Окончание
                                    @include('includes.inputs.date', [
                                        'name' => 'end_date',
                                        'value' => isset($outcome->end_date) ? $outcome->end_date->format('d.m.Y') : ''
                                    ]
                                    )
                                </label>
                            </div>

                            <div class="small-12 cell">
                                <search-client-component :client='@json($outcome->client)'></search-client-component>
                            </div>

                            <div class="small-12 cell">
                                <label>Описание
                                    @include('includes.inputs.textarea', ['name' => 'description'])
                                </label>
                            </div>

                        </div>

                    </div>

                    {{-- Правый блок на первой вкладке --}}
                    <div class="small-12 large-6 cell">
                        <div class="grid-x">
                            <photo-upload-component :photo='@json($outcome->photo)'></photo-upload-component>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-x grid-padding-x">
                {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $outcome])

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submit_text, ['class' => 'button']) }}
                </div>
            </div>


        </div>
    </div>
</div>
