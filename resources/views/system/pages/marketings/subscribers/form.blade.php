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
                                    @include('includes.inputs.name', ['required' => true])
                                </label>
                            </div>

                            <div class="small-12 medium-6 cell">
                                <label>Email
                                    @include('includes.inputs.email', ['name' => 'email'])
                                </label>
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

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitText, ['class' => 'button']) }}
                </div>
            </div>

        </div>
    </div>
</div>
