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
    <div class="grid-x grid-padding-x">
      <div class="small-12 cell">
        <label>Сотрудник:
          @php
          $block = null;
          @endphp
          @if (!empty($staffer->user_id))
          @php
          $block = 'disabled';
          @endphp
          @endif
          {{ Form::select('user_id', $users_list, $staffer->user_id, ['id'=>'staffer-select', 'placeholder'=>'Вакансия', $block]) }}
        </label>
      </div>
      <div class="small-6 cell">
        <label>Дата приема
          @php 
          $date_employment = null;
          @endphp
          @if (!empty($staffer))
          @foreach ($staffer->employees as $employee)
          @if (($employee->user_id == $staffer->user_id) && ($employee->date_dismissal == null))
          @php
          $date_employment = $employee->date_employment; 
          @endphp    
          @endif
          @endforeach
          {{ Form::text('date_employment', $date_employment, ['class'=>'date_employment date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off', 'required']) }}
          @endif
        </label>
      </div>
      <div class="small-6 cell">
        <label>Дата увольнения
          {{ Form::text('date_dismissal', null, ['class'=>'date_dismissal date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
        </label>
      </div>
      {{-- Чекбокс модерации --}}
      @can ('moderator', $staffer)
      @if ($staffer->moderation == 1)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.moderation', ['value'=>$staffer->moderation, 'name'=>'moderation'])
      </div>
      @endif
      @endcan

      {{-- Чекбокс системной записи --}}
      @can ('god', $staffer)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$staffer->system_item, 'name'=>'system_item']) 
      </div>
      @endcan  
    </div>
  </div>
  <div class="small-12 medium-5 large-5 cell tabs-margin-top">
    <label>Причина увольнения
      {{ Form::textarea('dismissal_desc', null, ['class'=>'varchar-field position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
  </div>
  <div class="small-0 medium-0 large-2 cell tabs-margin-top"></div>



  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

