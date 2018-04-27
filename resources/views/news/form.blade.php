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
    <label>Название новости
      @include('includes.inputs.string', ['name'=>'name', 'value'=>$cur_news->name, 'required'=>'required'])
    </label>
    <div class="grid-x grid-padding-x">
      <div class="small-6 cell">
        <label>Начало публикации
          @include('includes.inputs.date', ['name'=>'date_publish_begin', 'value'=>$cur_news->date_publish_begin, 'required'=>'required'])
        </label>
      </div>
      <div class="small-6 cell">
        <label>Окончание публикации
          @include('includes.inputs.date', ['name'=>'date_publish_end', 'value'=>$cur_news->date_publish_end, 'required'=>'required'])
        </label>
      </div>
    </div>
    <label>Заголовок новости
      @include('includes.inputs.string', ['name'=>'title', 'value'=>$cur_news->title, 'required'=>'required'])
    </label>
    <label>Превью новости
      @include('includes.inputs.textarea', ['name'=>'preview', 'value'=>$cur_news->preview, 'required'=>''])
    </label>

    <label>Выберите фото для превью
      {{ Form::file('photo') }}
    </label>
    <div class="text-center">
      <img id="photo" @if (isset($cur_news->photo_id)) src="/storage/{{ $cur_news->company->id }}/media/news/{{ $cur_news->id }}/original/{{ $cur_news->photo->name }}" @endif>
    </div>
    <label>Алиас новости
      @include('includes.inputs.text-en', ['name'=>'alias', 'value'=>$cur_news->alias, 'required'=>'required'])
    </label>
    @if (isset($cur_news->alias))
    <a class="button" href="http://{{ $cur_news->site->alias }}/news/{{ $cur_news->alias }}" target="_blank">Просмотр новости</a>
    @endif
    
    {{ Form::hidden('site_id', $site->id) }}
  </div>
  <div class="small-12 medium-5 large-7 cell">
    <label>Контент:
      {{ Form::textarea('content', $cur_news->сontent, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
    </label>
    <table class="table-content tabs-margin-top">
      <caption>Прикрепленные альбомы</caption>
      <thead>
        <tr>
          <th>Альбом</th>
          <th>Категория</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @if (!empty($cur_news->albums))
        @foreach ($cur_news->albums as $album)
        @include('news.albums', $album)
        @endforeach
        @endif
      </tbody>
    </table>
    {{ Form::hidden('cur_news_id', $cur_news->id, ['id' => 'cur-news-id']) }}
    <div class="text-center">
      <a class="button tabs-margin-top" data-open="album-add">Прикрепить альбом</a>
    </div>
  </div>

  {{-- Чекбокс модерации --}}
  @can ('moderator', $cur_news)
  @if ($cur_news->moderation == 1)
  <div class="small-12 cell checkbox">
    @include('includes.inputs.moderation', ['value'=>$cur_news->moderation, 'name'=>'moderation'])
  </div>
  @endif
  @endcan

  {{-- Чекбокс системной записи --}}
  @can ('god', $cur_news)
  <div class="small-12 cell checkbox">
    @include('includes.inputs.system', ['value'=>$cur_news->system_item, 'name'=>'system_item']) 
  </div>
  @endcan

  <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
  </div>
</div>


