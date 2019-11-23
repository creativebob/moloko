<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>

            @if($promotion->exists)
            <li class="tabs-title">
                <a data-tabs-target="photos" href="#photos">Фотки</a>
            </li>
@endif

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="options">

                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">

                        {{-- Сайт --}}
                        <label>Название
                            @include('includes.inputs.name', ['value' => $promotion->name, 'required' => true])
                        </label>

                        <label>Описание
                            @include('includes.inputs.textarea', ['name' => 'description', 'value' => $promotion->description])
                        </label>

                        <div class="grid-x grid-padding-x">
                            <div class="small-6 cell">
                                <label>Начало публикации
                                    @include('includes.inputs.date', [
                                        'name' => 'begin_date',
                                        'value' => isset($promotion->begin_date) ? $promotion->begin_date->format('d.m.Y') : '',
                                        'required' => true
                                    ]
                                    )
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Окончание публикации
                                    @include('includes.inputs.date', [
                                        'name' => 'end_date',
                                        'value' => isset($promotion->end_date) ? $promotion->end_date->format('d.m.Y') : ''
                                    ]
                                    )
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="small-12 medium-7 cell">


                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $promotion])

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button promotion-button']) }}
                    </div>
                </div>
            </div>

            @if($promotion->exists)
            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
@endpush

