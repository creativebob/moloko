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
      @include('includes.inputs.name', ['value'=>$site->name, 'name'=>'site_name', 'required'=>'required'])
    </label>
    <label>Домен сайта {{ $site->site_domen }}
      @include('includes.inputs.varchar', ['value'=>$site->domen, 'name'=>'site_domen', 'required'=>'required'])
      <div class="sprite-input-right find-status" id="name-check"></div>
      <div class="item-error">Такой сайт уже существует!</div>
    </label>
    {{ Form::hidden('check', 0, ['id'=>'check']) }}
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    <fieldset class="fieldset-access">
      <legend>Разделы сайта</legend>
      <ul>
        @foreach ($menus as $menu)
        <li class="checkbox"> 
          {{ Form::checkbox('menus[]', $menu->id, null, ['id'=>'menu-'.$menu->id]) }}
          <label for="menu-{{ $menu->id }}"><span>{{ $menu->name }}</span></label>
        </li>
        @endforeach
      </ul>
    </fieldset> 
  </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $site)
      @if ($site->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$site->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $site)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$site->system_item, 'name'=>'system_item']) 
      </div>
    @endcan   

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

