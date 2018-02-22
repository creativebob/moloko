

  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
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

          <div class="grid-x grid-padding-x"> 
            <div class="small-12 medium-6 cell">
              <label>Название группы
              {{ Form::text('role_name', $role->role_name, ['class'=>'string-field role-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Описание назначения группы
              {{ Form::text('role_description', $role->role_description, ['class'=>'varchar-field role-description-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>

      </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    @php
      $item = $role;
    @endphp
    {{-- Чекбокс модерации --}}
    @include('includes.inputs.moderation-checkbox', $item)
    {{-- Чекбокс системной записи --}}
    @include('includes.inputs.system-item-checkbox', $item)  
    
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

