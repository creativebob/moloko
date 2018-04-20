<!-- <div class="grid-x grid-padding-x inputs">
  <div class="small-12 medium-6 large-5 cell tabs-margin-top">
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
      <div class="grid-x">
        <div class="small-12 cell">
          <label>Название фото
            @include('includes.inputs.name', ['name'=>'name', 'value'=>$photo->name, 'required'=>'required'])
          </label>
        </div>
      </div>
  </div>
  <div class="small-12 medium-6 large-7 cell tabs-margin-top">
    <div class="grid-x">
      <div class="small-12 cell">
          <label>Выберите аватар
            {{ Form::file('photo') }}
          </label>
          <div class="text-center">
            <img id="photo" src="{{ isset($photo->path) ? url($photo->path) : 'lol' }}">
          </div>
        </div>
        <div class="small-12 cell dropzone" id="my-dropzone">
                <h3>Перетащите фото сюда</h3>
            </div>
    </div>
  </div>

  {{-- Чекбокс модерации --}}
  @can ('moderator', $photo)
  @if ($photo->moderation == 1)
  <div class="small-12 small-text-center cell checkbox">
    @include('includes.inputs.moderation', ['value'=>$photo->moderation, 'name'=>'moderation'])
  </div>
  @endif
  @endcan

  {{-- Чекбокс системной записи --}}
  @can ('god', $photo)
  <div class="small-12 cell checkbox">
    @include('includes.inputs.system', ['value'=>$photo->system_item, 'name'=>'system_item'])
  </div>
  @endcan    

  <div class="small-12 small-text-center medium-text-left cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div> -->






