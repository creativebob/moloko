<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-general" aria-selected="true">Информация</a>
            </li>
        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div data-tabs-content="tabs">

            {{-- Общая информация --}}
            <div class="tabs-panel is-active" id="tab-general">
                @include('system.pages.labels.tabs.general')
            </div>

            <div class="grid-x grid-padding-x">

                {!! Form::hidden('is_external', 0) !!}
                <div class="cell small-12 checkbox">
                    {!! Form::checkbox('is_external', 1, $label->is_external, ['id' => 'checkbox-is_external']) !!}
                    <label for="checkbox-is_external"><span>Внешняя</span></label>
                </div>

                {!! Form::hidden('is_internal', 0) !!}
                <div class="cell small-12 checkbox">
                    {!! Form::checkbox('is_internal', 1, $label->is_internal, ['id' => 'checkbox-is_internal']) !!}
                    <label for="checkbox-is_internal"><span>Внутренняя</span></label>
                </div>

                {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $label])

                <div
                    class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitText, ['class' => 'button']) }}
                </div>
            </div>


        </div>
    </div>
</div>
