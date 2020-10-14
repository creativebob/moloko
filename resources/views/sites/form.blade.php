<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#main" aria-selected="true">Общая информация</a>
            </li>
            <li class="tabs-title">
                <a href="#options" aria-selected="true">Настройки</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="main">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-5 cell">

                        {{-- Сайт --}}
                        <label>Название сайта
                            @include('includes.inputs.name', ['value' => $site->name, 'required' => true])
                        </label>

                        <label>Алиас
                            @include('includes.inputs.name', ['name' => 'alias', 'value' => $site->alias])
                        </label>

                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $site])

                    <div class="small-12 cell">
                        <div class="item-error" id="filial-error">Выберите минимум 1 филиал!</div>
                    </div>

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button site-button']) }}
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="options">
                <div class="grid-x grid-padding-x">
                    <div class="small-12 medium-5 cell">

                        <div class="cell small-12 checkbox">
                            {!! Form::hidden('is_autochange', 0) !!}
                            {!! Form::checkbox('is_autochange', 1, $site->is_autochange, ['id' => 'checkbox-is_autochange']) !!}
                            <label for="checkbox-is_autochange"><span>Авто-смена слайдов</span></label>
                            <br>
                        </div>

                        <div class="cell small-6 medium-6">
                            <label>Время задержки, мс
                                {!! Form::number('delay', $site->delay) !!}
                            </label>
                        </div>

                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $site])

                    <div class="small-12 cell">
                        <div class="item-error" id="filial-error">Выберите минимум 1 филиал!</div>
                    </div>

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button site-button']) }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.check', ['entity' => 'sites'])

{{--@include('sites.scripts')--}}
@endpush

