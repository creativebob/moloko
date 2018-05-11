<div class="grid-x grid-padding-x modal-content inputs">
  <div class="small-10 small-offset-1 cell">
    <label>Расположение
      <select name="parent_id">
        @php
          echo $albums_categories_list;
        @endphp
      </select>
    </label>
    <label>Название категории
      @include('includes.inputs.name', ['value'=>$albums_category->name, 'name'=>'name', 'required'=>'required'])
      <div class="sprite-input-right find-status"></div>
      <div class="item-error">Такой сектор уже существует!</div>
    </label>
    {{ Form::hidden('albums_category_id', $albums_category->id, ['id' => 'albums-category-id']) }}
    {{ Form::hidden('medium_item', 0, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
    @if ($albums_category->moderation == 1)
      <div class="checkbox">
        {{ Form::checkbox('moderation', 1, $albums_category->moderation, ['id' => 'moderation']) }}
        <label for="moderation"><span>Временная запись.</span></label>
      </div>
      @endif
      @can('god', App\AlbumsCategory::class)
      <div class="checkbox">
        {{ Form::checkbox('system_item', 1, $albums_category->system_item, ['id' => 'system-item']) }}
        <label for="system-item"><span>Системная запись.</span></label>
      </div>
      @endcan
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-6 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>