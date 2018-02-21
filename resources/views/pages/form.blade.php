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
    {{ Form::text('page_name', $page->page_name, ['class'=>'string-mask page-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <label>Заголовок страницы
    {{ Form::text('page_title', $page->page_title, ['class'=>'string-mask page-title-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <label>Описание страницы
    {{ Form::textarea('page_description', $page->page_description, ['class'=>'varchar-mask page-description-field', 'autocomplete'=>'off', 'size' => '10x3']) }}
    </label>
    <label>Алиас страницы
    {{ Form::text('page_alias', $page->page_alias, ['class'=>'text-en-mask page-alias-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <input type="hidden" name="site_id" value="{{ $current_site->id }}">
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">

  </div>

    @can ('moderator', $page)
      @if ($page->moderated == 1)
        <div class="small-12 cell checkbox">
          {{ Form::checkbox('moderation_status', null, $page->moderated, ['id'=>'moderation-checkbox']) }}
          <label for="moderation-checkbox"><span>Временная запись!</span></label>
        </div>
      @endif
    @endcan

    @can ('god', $page)
      <div class="small-12 cell checkbox">
        {{ Form::checkbox('system_item', null, $page->system_item, ['id'=>'system-checkbox']) }}
        <label for="system-checkbox"><span>Сделать запись системной.</span></label>
      </div>
    @endcan
  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

