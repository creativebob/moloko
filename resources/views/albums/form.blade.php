


  <div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-5 cell tabs-margin-top">
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
              <label>Название альбома
                @include('includes.inputs.name', ['name'=>'second_name', 'value'=>$user->second_name, 'required'=>'required'])
              </label>
            </div>




            <div calss="small-12 medium-6 cell">
              {{ Form::file('photo') }}
              <img id="photo">
            </div>
          </div>

          <div class="grid-x grid-padding-x tabs-margin-top">
            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$user->phone, 'name'=>'phone', 'required'=>'required'])
              </label>
            </div>
            <div class="small-12 medium-6 cell">
              <label>Телефон
                @include('includes.inputs.phone', ['value'=>$user->extra_phone, 'name'=>'extra_phone', 'required'=>''])
              </label>
            </div>
          </div>

          {{-- Чекбокс модерации --}}
          @can ('moderator', $user)
            @if ($user->moderation == 1)
              <div class="small-12 small-text-center cell checkbox">
                @include('includes.inputs.moderation', ['value'=>$user->moderation, 'name'=>'moderation'])
              </div>
            @endif
          @endcan

          {{-- Чекбокс системной записи --}}
          @can ('god', $user)
            <div class="small-12 cell checkbox">
              @include('includes.inputs.system', ['value'=>$user->system_item, 'name'=>'system_item'])
            </div>
          @endcan    

    <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>


  

