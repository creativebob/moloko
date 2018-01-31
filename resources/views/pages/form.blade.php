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
    {{ Form::text('page_name', $page->page_name, ['class'=>'page-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <label>Заголовок страницы
    {{ Form::text('page_title', $page->page_title, ['class'=>'page-title-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <label>Описание страницы
    {{ Form::textarea('page_description', $page->page_description, ['class'=>'page-description-field', 'autocomplete'=>'off', 'size' => '10x3']) }}
    </label>
    <label>Алиас страницы
    {{ Form::text('page_alias', $page->page_alias, ['class'=>'page-alias-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
    </label>
    <input type="hidden" name="site_id" value="{{ $current_site->id }}">
  </div>
  <div class="small-12 medium-5 large-7 cell tabs-margin-top">

  </div>
  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

