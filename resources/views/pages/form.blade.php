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
    <!-- Страница -->
    <label>Название страницы
      @include('includes.inputs.string', ['name'=>'page_name', 'value'=>$page->page_name, 'required'=>'required'])
    </label>
    <label>Заголовок страницы
      @include('includes.inputs.string', ['name'=>'page_title', 'value'=>$page->page_title, 'required'=>'required'])
    </label>
    <label>Описание страницы
      @include('includes.inputs.textarea', ['name'=>'page_description', 'value'=>$page->page_description, 'required'=>''])
    </label>
    <label>Алиас страницы
      @include('includes.inputs.text-en', ['name'=>'page_alias', 'value'=>$page->page_alias, 'required'=>'required'])
    </label>
    {{ Form::hidden('site_id', $current_site->id) }}
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">

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

