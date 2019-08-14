

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
                @include('includes.inputs.text-ru', ['value'=>$entity->name, 'name'=>'name', 'required' => true])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Название сущности в BD
                @include('includes.inputs.text-en', ['value'=>$entity->alias, 'name'=>'alias', 'required' => true])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Имя модели во фреймворке
                @include('includes.inputs.text-en', ['value'=>$entity->model, 'name'=>'model', 'required' => true])
              </label>
            </div>
            <div class="small-6 cell radiobutton">Генерировать права?<br>

              {{ Form::radio('rights_minus', 0, true, ['id'=>'Yes']) }}
              <label for="Yes"><span>Да</span></label>

              {{ Form::radio('rights_minus', 1, false, ['id'=>'No']) }}
              <label for="No"><span>Нет</span></label>

            </div>
          </div>

      </div>
    </div>
    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $entity)
      @if ($entity->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$entity->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $entity)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$entity->system, 'name'=>'system'])
      </div>
    @endcan

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

