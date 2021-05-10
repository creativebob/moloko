<div class="grid-x">
    <div class="cell small-12 medium-7 large-5">

        <div class="grid-x grid-x grid-margin-x">

            <div class="cell small-12">
                <select-processes-categories-component
                    :select-categories='@json($categoriesTree)'
                    :select-categories-items='@json($items)'
                    @isset($flow->process)
                    :item="{{ $flow->process }}"
                    @endisset
                ></select-processes-categories-component>
            </div>

            <div class="cell medium-6">
                <div class="grid-x grid-margin-x">
                    <div class="cell medium-6">
                        <label>Дата начала
                            <pickmeup-component
                                name="start_date"
                                value="{{ optional($flow->start_at)->format('Y-m-d') }}"
                                :required="true"
                            ></pickmeup-component>
                        </label>
                    </div>
                    <div class="cell medium-6">
                        <label>Время начала:
                            @include('includes.inputs.time', ['name' => 'start_time', 'placeholder' => true, 'value' => optional($flow->start_at)->format('H:i'), 'required' => null])
                        </label>
                    </div>
                </div>

                <div class="grid-x grid-margin-x">
                    <div class="cell medium-6">
                        <label>Дата окончания
                            <pickmeup-component
                                name="finish_date"
                                value="{{ optional($flow->finish_at)->format('Y-m-d') }}"
                                :required="true"
                            ></pickmeup-component>
                        </label>
                    </div>
                    <div class="cell medium-6">
                        <label>Время окончания:
                            @include('includes.inputs.time', ['name' => 'finish_time', 'placeholder' => true, 'value' => optional($flow->finish_at)->format('H:i'), 'required' => null])
                        </label>
                    </div>
                </div>

                <div class="grid-x grid-margin-x">
                    <div class="cell medium-6">
                        <label>Минимум человек
                            <digit-component
                                name="capacity_min"
                                @isset($flow->capacity_min)
                                value="{{ $flow->capacity_min }}"
                                @endisset
                                :decimalplace="0"
                            ></digit-component>
                        </label>
                    </div>
                    <div class="cell medium-6">
                        <label>Максимум человек
                            <digit-component
                                name="capacity_max"
                                @isset($flow->capacity_max)
                                value="{{ $flow->capacity_max }}"
                                @endisset
                                :decimalplace="0"
                            ></digit-component>
                        </label>
                    </div>
                </div>
            </div>

            <div class="cell medium-6">
                @include('system.common.includes.city_search', ['item' => $flow])
                <label>Адрес
                    @include('includes.inputs.address', ['value' => optional($flow->location)->address, 'name' => 'address'])
                </label>
                <label>Почтовый индекс
                    @include('includes.inputs.zip_code', ['value'=>optional($flow->location)->zip_code, 'name' => 'zip_code'])
                </label>
            </div>

            <div class="cell medium-6">
                @if(session("access.all_rights.index-{$processAlias}-allow.filials_for_user")->count() > 1)
                    <label>Филиал:
                        {!! Form::select('filial_id', session("access.all_rights.index-{$processAlias}-allow.filials_for_user")->pluck('name', 'id'), $flow->filial_id) !!}
                    </label>
                @else
                    <input
                        type="hidden"
                        name="filial_id"
                        value="{{ session("access.all_rights.index-{$processAlias}-allow.filials_for_user")->first()->id }}"
                    >
                @endif
            </div>

        </div>

    </div>
</div>
