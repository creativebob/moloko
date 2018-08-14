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
    <label>Контент:
      {{ Form::textarea('content', $page->content, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
    </label>
    {{ Form::hidden('check', 0, ['id'=>'check']) }}
    {{ Form::hidden('site_id', $site->id) }}
  </div>
  <div class="small-12 medium-5 large-7 cell">

    <label>Выберите аватар
      {{ Form::file('photo') }}
    </label>
    <div class="text-center">
      <img id="photo" @if (isset($page->photo_id)) src="/storage/{{ $site->company_id }}/media/pages/{{ $page->id }}/img/medium/{{ $page->photo->name }}" @endif>
    </div>
  </div>

  {{-- Чекбоксы управления --}}
  @include('includes.control.checkboxes', ['item' => $page])   

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>

