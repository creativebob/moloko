<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>

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
                        <label>Название сайта
                            @include('includes.inputs.name', ['value' => $site->name, 'required' => true])
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

        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.check', ['entity' => 'sites'])

{{--@include('sites.scripts')--}}
@endpush

