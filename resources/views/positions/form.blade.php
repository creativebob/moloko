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
    @php
      $block = null;
    @endphp
    @if ($position->system_item == 1)
      @php
        $block = 'readonly';
      @endphp
    @endif
    {{ Form::text('position_name', null, ['class'=>'text-ru-field position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off', $block]) }}
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
                  
                  {{ Form::checkbox('roles[]', $role->id, null, ['id'=>'role-'.$role->id]) }}
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

    @php
      $item = $position;
    @endphp
    {{-- Чекбокс модерации --}}
    @include('includes.inputs.moderation-checkbox', $item)
    {{-- Чекбокс системной записи --}}
    @include('includes.inputs.system-item-checkbox', $item)  

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

