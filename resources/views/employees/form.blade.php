<div class="grid-x grid-padding-x inputs">
  <div class="small-12 medium-7 large-5 cell tabs-margin-top">
    @if ($errors->any())
      <div class="alert callout" data-closable>
        <h5>Неправильный формат данных:</h5>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif
    <!-- Сотрудник -->
    <label>Название должности
    {{ Form::text('position_name', $employee->staffer->position->name, ['class'=>'varchar-field position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'readonly']) }}
    </label>
    <label>Сотрудник:
      {{ Form::select('user_id', $users_list, $employee->user_id, ['id'=>'staffer-select', 'placeholder'=>'Вакансия', 'disabled']) }}
    </label>
    <div class="grid-x">
      <div class="small-12 medium-5 cell">
        <label>Дата приема
          @include('includes.inputs.date', ['value'=>$employee->employment_date, 'name'=>'employment_date', 'required' => true])
        </label>
      </div>
      <div class="small-12 medium-5 medium-offset-1 cell">
        <label>Дата увольнения
          @include('includes.inputs.date', ['value'=>$employee->dismissal_date, 'name'=>'dismissal_date'])
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

