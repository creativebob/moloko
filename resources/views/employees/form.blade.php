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
    {{ Form::text('position_name', $employee->staffer->position->position_name, ['class'=>'position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off', 'readonly']) }}
    </label>
    <label>Сотрудник:
      {{ Form::select('user_id', $users_list, $employee->user_id, ['id'=>'staffer-select', 'placeholder'=>'Вакансия', 'disabled']) }}
    </label>
    <div class="grid-x">
      <div class="small-12 medium-5 cell">
        <label>Дата приема
          {{ Form::text('date_employment', $employee->date_employment, ['class'=>'date_employment date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
        </label>
      </div>
      <div class="small-12 medium-5 medium-offset-1 cell">
        <label>Дата увольнения
          {{ Form::text('date_dismissal', $employee->date_dismissal, ['class'=>'date_dismissal date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
        </label>
      </div>
    </div>
    <label>Причина увольнения
    {{ Form::text('dismissal_desc', $employee->dismissal_desc, ['class'=>'position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">

  </div>
  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

