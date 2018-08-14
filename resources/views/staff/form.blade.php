<div class="grid-x tabs-wrap">
  <div class="small-12 cell">
    <ul class="tabs-list" data-tabs id="tabs">
      <li class="tabs-title is-active"><a href="#settings" aria-selected="true">Общая информация</a></li>
      <li class="tabs-title"><a data-tabs-target="worktimes" href="#worktimes">График работы</a></li>
    </ul>
  </div>
</div>

<div class="tabs-wrap inputs">
  <div class="tabs-content" data-tabs-content="tabs">

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

    <!-- Общая информация -->
    <div class="tabs-panel is-active" id="settings">
      <div class="grid-x grid-padding-x inputs">
        <div class="small-12 medium-7 large-5 cell tabs-margin-top">
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
                $employment_date = null;
                @endphp
                @if (!empty($staffer))
                @foreach ($staffer->employees as $employee)
                @if (($employee->user_id == $staffer->user_id) && ($employee->dismissal_date == null))
                @php
                $employment_date = $employee->employment_date; 
                @endphp    
                @endif
                @endforeach
                {{ Form::text('employment_date', $employment_date, ['class'=>'employment_date date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off', 'required']) }}
                @endif
              </label>
            </div>
            <div class="small-6 cell">
              <label>Дата увольнения
                {{-- @include('includes.inputs.date', ['name'=>'dismissal_date', 'value'=>$user->birthday, 'required'=>'']) --}}
                {{ Form::text('dismissal_date', null, ['class'=>'dismissal_date date-field', 'pattern'=>'[0-9]{2}.[0-9]{2}.[0-9]{4}', 'autocomplete'=>'off']) }}
              </label>
            </div>

            {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $staffer]) 
          </div>
        </div>
        <div class="small-12 medium-5 large-5 cell tabs-margin-top">
          <label>Причина увольнения
            {{ Form::textarea('dismissal_desc', null, ['class'=>'varchar-field position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
          </label>
        </div>
        <div class="small-0 medium-0 large-2 cell tabs-margin-top"></div>
      </div> 
    </div>

    <!-- График работы -->
    <div class="tabs-panel" id="worktimes">
      <div class="grid-x grid-padding-x">
        <div class="small-12 medium-6 cell tabs-margin-top">
          @include('includes.inputs.schedule', ['value'=>$worktime]) 
        </div>
      </div>
    </div>

  </div>
</div>


<div class="grid-x grid-padding-x">
<div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
  {{ Form::submit($submitButtonText, ['class'=>'button']) }}
</div>
</div>

