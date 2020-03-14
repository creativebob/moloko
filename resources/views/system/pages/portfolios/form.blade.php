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
                        <label>Название
                            @include('includes.inputs.name', ['value' => $portfolio->name, 'required' => true])
                        </label>

                        <label>Описание
                            @include('includes.inputs.textarea', ['name' => 'description'])
                        </label>

                    </div>

                    <div class="cell small-12 medium-7">
                        <photo-upload-component :photo='@json($portfolio->photo)'></photo-upload-component>
                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $portfolio])

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button portfolio-button']) }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.check', ['entity' => 'portfolios'])
@endpush

