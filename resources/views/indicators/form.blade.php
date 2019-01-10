
<div class="grid-x grid-padding-x inputs">

    <div class="small-12 medium-6 cell tabs-margin-top">

        <div class="grid-x grid-padding-x">

            <div class="small-12 cell">

                <label>Название
                    @include('includes.inputs.name', ['required' => true])
                    <div class="sprite-input-right find-status" id="alias-check"></div>
                    <div class="item-error">Такой показатель уже существует!</div>
                </label>

                        {{-- <label class="alias">Алиас альбома
                            @include('includes.inputs.name', ['name' => 'alias', 'check' => true])
                            <div class="sprite-input-right find-status" id="alias-check"></div>
                            <div class="item-error">Альбом с таким алиасом уже существует!</div>
                        </label> --}}

                    </div>

                    <div class="small-12 medium-6 cell">

                        <label>Категория
                            @include('includes.selects.indicators_categories', ['indicators_category_id' => $indicator->indicators_category_id])
                        </label>

                    </div>

                    <div class="small-12 medium-6 cell">

                        <label>Сущность
                            @include('includes.selects.entities_statistics', ['entity_id' => $indicator->indicators_category_id])
                        </label>

                    </div>

                    <div class="small-12 cell">
                        <div class="grid-x grid-margin-x">
                            <div class="small-12 medium-4 cell">
                                @include('includes.selects.units_categories', ['default' => 6])
                            </div>

                            <div class="small-12 medium-4 cell">
                                @include('includes.selects.units', ['default' => 26, 'units_category_id' => 6])
                            </div>

                            <div class="small-12 medium-4 cell">

                                @include('includes.selects.periods', ['default' => 3, 'period_id' => 3, 'disabled' => isset($form_method)])
                            </div>

                        </div>
                    </div>

                    <div class="small-12 cell">
                        <label>Описание
                            @include('includes.inputs.textarea', ['name' => 'description'])
                        </label>
                    </div>
                </div>
            </div>

            <div class="small-12 medium-5 large-7 cell tabs-margin-top">
            </div>

            {{-- Чекбоксы управления --}}
            @include('includes.control.checkboxes', ['item' => $indicator])

            <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                {{ Form::submit($submit_text, ['class'=>'button']) }}
            </div>
        </div>

