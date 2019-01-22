<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell">
                    <label>Название группы
                        @include('includes.inputs.string', ['name'=>'name', 'value'=>$role->name, 'required' => true])
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Описание назначения группы
                        @include('includes.inputs.varchar', ['name'=>'description', 'value'=>$role->description])
                    </label>
                </div>
            </div>

        </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $role])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
</div>

