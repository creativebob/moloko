

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
                @include('includes.inputs.string', ['name'=>'role_name', 'value'=>$role->role_name])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Описание назначения группы
                @include('includes.inputs.varchar', ['name'=>'role_description', 'value'=>$role->role_description])
              </label>
            </div>
          </div>

      </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $role)
      @if ($role->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$role->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $role)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system_item', ['value'=>$role->system_item, 'name'=>'system_item']) 
      </div>
    @endcan   
    
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

