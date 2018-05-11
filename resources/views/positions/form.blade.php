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
    <!-- Должность -->
    <label>Название должности
      @include('includes.inputs.name', ['value'=>$position->name, 'name'=>'name', 'required'=>'required'])
      <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
    </label>
    <label>Страница должности:
      {{ Form::select('page_id', $pages_list, null, ['id'=>'page-select']) }}
    </label>
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    <fieldset class="fieldset-access">
        <legend>Настройка доступа</legend>
        <div class="grid-x grid-padding-x"> 
          <div class="small-12 cell">
            <ul>
              @foreach ($roles as $role)
              <li>
                <div class="small-12 cell checkbox">
                  {{ Form::checkbox('roles[]', $role->id, null, ['id'=>'role-'.$role->id, 'class'=>'access-checkbox']) }}
                  <label for="role-{{ $role->id }}"><span>{{ $role->role_name }}</span></label>
                  @php
                    $allow = count($role->rights->where('directive', 'allow'));
                    $deny = count($role->rights->where('directive', 'deny'));
                  @endphp
                  <span class="allow">{{ $allow }}</span> / <span class="deny">{{ $deny }}</span>
                </div>
              </li>
              @endforeach
            </ul>

          </div>
        </div>
      </fieldset> 
  </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $position)
      @if ($position->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$position->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $position)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$position->system_item, 'name'=>'system_item']) 
      </div>
    @endcan   

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button position-button', 'disabled']) }}
  </div>
</div>

