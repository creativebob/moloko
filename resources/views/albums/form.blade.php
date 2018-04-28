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
    
    <div class="grid-x grid-padding-x">
      <div class="small-12 medium-6 cell">
        <label>Название альбома
          @include('includes.inputs.name', ['value'=>$album->name, 'name'=>'name', 'required'=>'required'])
        </label>
        <label>Алиас альбома
          @include('includes.inputs.name', ['value'=>$album->alias, 'name'=>'alias', 'required'=>'required'])
        </label>
      </div>
      <div class="small-12 medium-6 cell">
        <label>Категория ольбома
          <select name="albums_category_id">
            @php
            echo $albums_categories_list;
            @endphp
          </select>
        </label>
      </div>
      <div class="small-12 cell">
        <label>Описание альбома
          @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$album->description, 'required'=>''])
        </label>
      </div>
    </div>
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    @if (isset($album->avatar))
      <img src="/storage/{{ $album->company->company_alias }}/media/albums/{{ $album->alias }}/{{ $album->avatar }}">
    @endif
  </div>

  <div class="small-12 small-text-center cell checkbox">
    {{ Form::checkbox('access', 1, null, ['id'=>'private-checkbox']) }}
    <label for="private-checkbox"><span>Личный альбом.</span></label>
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
