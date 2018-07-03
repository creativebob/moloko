

  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
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
              <div class="grid-x grid-padding-x">

                <div class="small-12 medium-12 cell">
                  <label>Название помещения
                    @include('includes.inputs.string', ['name'=>'name', 'value'=>$place->name, 'required'=>'required'])
                  </label>
                </div>
                <div class="small-12 medium-12 cell">
                  <label>Описание
                    @include('includes.inputs.varchar', ['name'=>'description', 'value'=>$place->description, 'required'=>''])
                  </label>
                </div>

                <div class="small-12 medium-6 cell">
                  <label>Площадь, м2
                    @include('includes.inputs.varchar', ['name'=>'square', 'value'=>$place->square, 'required'=>'required'])
                  </label>
                </div>
                <div class="small-12 medium-6 cell">
                  <label>Форма владения
                    @include('includes.inputs.string', ['name'=>'rent_status', 'value'=>$place->rent_status, 'required'=>''])
                  </label>
                </div>

              </div>
            </div>

          </div>

      </div>
    </div>
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $place)
      @if ($place->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$place->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $place)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$place->system_item, 'name'=>'system_item']) 
      </div>
    @endcan   
    
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>

