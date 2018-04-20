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
    <!-- Сайт -->
    <label>Название сайта
      @include('includes.inputs.name', ['value'=>$album->name, 'name'=>'site_name', 'required'=>'required'])
    </label>
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    <fieldset class="fieldset-access">
      <legend>Разделы сайта</legend>
      <ul>

      </ul>
    </fieldset> 
  </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $album)
      @if ($album->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$album->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $album)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$album->system_item, 'name'=>'system_item']) 
      </div>
    @endcan   

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>
