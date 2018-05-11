<div class="grid-x grid-padding-x inputs tabs-margin-top">
  <div class="small-12 medium-7 large-5 cell ">
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
    <!-- Страница -->
    <label>Название страницы
      @include('includes.inputs.string', ['name'=>'name', 'value'=>$page->name, 'required'=>'required'])
    </label>
    <label>Заголовок страницы
      @include('includes.inputs.string', ['name'=>'title', 'value'=>$page->title, 'required'=>'required'])
    </label>
    <label>Описание страницы
      @include('includes.inputs.textarea', ['name'=>'description', 'value'=>$page->description, 'required'=>''])
    </label>
    <label>Алиас страницы
      @include('includes.inputs.text-en', ['name'=>'alias', 'value'=>$page->alias, 'required'=>'required'])
      <div class="sprite-input-right find-status" id="name-check"></div>
      <div class="item-error">Такая страница уже существует!</div>
    </label>
    {{ Form::hidden('check', 0, ['id'=>'check']) }}
    {{ Form::hidden('site_id', $site->id) }}
  </div>
  <div class="small-12 medium-5 large-7 cell">
    <label>Контент:
      {{ Form::textarea('page_content', $page->content, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
    </label>
  </div>

    {{-- Чекбокс модерации --}}
    @can ('moderator', $page)
      @if ($page->moderation == 1)
        <div class="small-12 cell checkbox">
          @include('includes.inputs.moderation', ['value'=>$page->moderation, 'name'=>'moderation'])
        </div>
      @endif
    @endcan

    {{-- Чекбокс системной записи --}}
    @can ('god', $page)
      <div class="small-12 cell checkbox">
        @include('includes.inputs.system', ['value'=>$page->system_item, 'name'=>'system_item']) 
      </div>
    @endcan   

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

