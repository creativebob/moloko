



  <div class="grid-x tabs-wrap">
    <div class="small-12 cell">
      <ul class="tabs-list" data-tabs id="tabs">
        <li class="tabs-title is-active"><a href="#content-panel-1" aria-selected="true">Учетные данные</a></li>
      </ul>
    </div>
  </div>

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

        <!-- Учетные данные -->
        <div class="tabs-panel is-active" id="content-panel-1">
          <div class="grid-x grid-padding-x">


            <div class="grid-x grid-padding-x">
              <div class="small-12 cell tabs-margin-top">
                <label>Сущность базы данных
                  {{ Form::select('entity_id', $entities_list, $right->entity_id) }}
                </label>
              </div>
              <div class="small-12 cell checkbox">
                  {{ Form::checkbox('access_block', 1, 'view', ['id'=>'checkbox_view']) }}
                <label for="checkbox_view"><span>Просмотр записи</span></label>
              </div>
              <div class="small-12 cell checkbox">
                  {{ Form::checkbox('access_block', 1, 'index', ['id'=>'checkbox_index']) }}
                <label for="checkbox_view"><span>Просмотр списка записей</span></label>
              </div>
              <div class="small-12 cell checkbox">
                  {{ Form::checkbox('access_block', 1, 'edit', ['id'=>'checkbox_edit']) }}
                <label for="checkbox_view"><span>Редактирование записи</span></label>
              </div>
              <div class="small-12 cell checkbox">
                  {{ Form::checkbox('access_block', 1, 'create', ['id'=>'checkbox_create']) }}
                <label for="checkbox_view"><span>Добавление записи</span></label>
              </div>
              <div class="small-12 cell checkbox">
                  {{ Form::checkbox('access_block', 1, 'delete', ['id'=>'checkbox_delete']) }}
                <label for="checkbox_view"><span>Удаление записи</span></label>
              </div>



            </div>

          </div>

        </div>

      </div>
    </div>
    <div class="small-12 medium-4 medium-offset-1 large-4 large-offset-3 cell">
      <fieldset class="fieldset-access">
        <legend>Настройка доступа</legend>

      </fieldset> 
    </div>

    @can ('moderator', $right)
      @if ($right->moderated == 1)
        <div class="small-12 cell checkbox">
          {{ Form::checkbox('moderation_status', null, $right->moderated, ['id'=>'moderation-checkbox']) }}
          <label for="moderation-checkbox"><span>Временная запись!</span></label>
        </div>
      @endif
    @endcan

    @can ('god', $right)
      <div class="small-12 cell checkbox">
        {{ Form::checkbox('system_item', null, $right->system_item, ['id'=>'system-checkbox']) }}
        <label for="system-checkbox"><span>Сделать запись системной.</span></label>
      </div>
    @endcan
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

