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
                            @include('includes.inputs.name', ['value' => $dispatch->name, 'required' => true])
                        </label>

                        <label>Текст
                            @include('includes.inputs.textarea', ['name' => 'body', 'value' => $dispatch->body])
                        </label>

                        <label>
                            Каналы
                            @include('includes.selects.channels')
                        </label>

                    </div>

                    <div class="small-12 medium-7 cell">

                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $dispatch])

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button dispatch-button']) }}
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="photos">

            </div>
        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@endpush

