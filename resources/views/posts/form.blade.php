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
    <label>Название поста
      @include('includes.inputs.varchar', ['name'=>'name', 'value'=>$post->name, 'required'=>'required'])
    </label>
    <div class="grid-x grid-padding-x">
      <div class="small-6 cell">
        <label>Начало публикации
          @include('includes.inputs.date', ['name'=>'publish_begin_date', 'value'=>$post->publish_begin_date, 'required'=>'required'])
        </label>
      </div>
      <div class="small-6 cell">
        <label>Окончание публикации
          @include('includes.inputs.date', ['name'=>'publish_end_date', 'value'=>$post->publish_end_date, 'required'=>''])
        </label>
      </div>
    </div>
    <label>Заголовок поста
      @include('includes.inputs.varchar', ['name'=>'title', 'value'=>$post->title, 'required'=>'required'])
    </label>
    <label>Пост коротко
      @include('includes.inputs.textarea', ['name'=>'preview', 'value'=>$post->preview, 'required'=>''])
    </label>

    <label>Выберите фото для превью
      {{ Form::file('photo') }}
    </label>
    <div class="text-center">
      <img id="photo" @if (isset($post->photo_id)) src="/storage/{{ $post->company->id }}/media/posts/{{ $post->id }}/img/original/{{ $post->photo->name }}" @endif>
    </div>
    {{ Form::hidden('check', 0, ['id'=>'check']) }}
  </div>
  <div class="small-12 medium-5 large-7 cell">
    <label>Контент:
      {{ Form::textarea('content', $post->сontent, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
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
        @if (!empty($post->albums))
        @foreach ($post->albums as $album)
        @include('posts.albums', $album)
        @endforeach
        @endif
      </tbody>
    </table>
    {{ Form::hidden('cur_post_id', $post->id, ['id' => 'cur-posts-id']) }}
    <div class="text-center">
      <a class="button tabs-margin-top" data-open="album-add">Прикрепить альбом</a>
    </div>

    <div class="grid-x">
      <div class="small-12 medium-6 cell">

      </div>
    </div>

    </div>

    {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $post]) 

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
      {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
  </div>


