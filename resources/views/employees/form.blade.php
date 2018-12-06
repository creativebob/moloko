<div class="grid-x grid-padding-x inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">

        <!-- Сотрудник -->
        <label>Название должности
            {{ Form::text('position_name', $employee->staffer->position->name, ['class'=>'varchar-field position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'readonly']) }}
        </label>
        <label>Сотрудник:
            @include('includes.selects.staff', ['disabled' => true, 'mode' => 'default'])
        </label>
        <div class="grid-x">
            <div class="small-12 medium-5 cell">
                <label>Дата приема
                    @include('includes.inputs.date', ['value'=>$employee->employment_date->format('d.m.Y'), 'name'=>'employment_date', 'required' => true])
                </label>
            </div>
            <div class="small-12 medium-5 medium-offset-1 cell">
                <label>Дата увольнения
                    @include('includes.inputs.date', ['value'=>$employee->dismissal_date->format('d.m.Y'), 'name'=>'dismissal_date', 'required' => true])
                </label>
            </div>
        </div>
        <label>Причина увольнения
            @include('includes.inputs.name', ['value'=>$employee->dismissal_description, 'name'=>'dismissal_description'])
        </label>

    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">

    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $employee])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
</div>

