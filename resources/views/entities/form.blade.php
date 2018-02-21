

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
              <label>Название сущности
              {{ Form::text('entity_name', $entity->entity_name, ['class'=>'text-ru-mask entity-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Название сущности в BD
              {{ Form::text('entity_alias', $entity->entity_alias, ['class'=>'text-en-mask entity-alias-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
              </label>
            </div>
          </div>

      </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    @can ('moderator', $entity)
      @if ($entity->moderated == 1)
        <div class="small-12 cell checkbox">
          {{ Form::checkbox('moderation_status', null, $entity->moderated, ['id'=>'moderation-checkbox']) }}
          <label for="moderation-checkbox"><span>Временная запись!</span></label>
        </div>
      @endif
    @endcan

    @can ('god', $entity)
      <div class="small-12 cell checkbox">
        {{ Form::checkbox('system_item', null, $entity->system_item, ['id'=>'system-checkbox']) }}
        <label for="system-checkbox"><span>Сделать запись системной.</span></label>
      </div>
    @endcan


    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

