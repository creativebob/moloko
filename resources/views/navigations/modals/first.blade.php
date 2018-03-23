<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label class="input-icon">Введите название навигации
      @include('includes.inputs.name', ['value'=>$navigation->name, 'name'=>'navigation_name'])
      <div class="sprite-input-right find-status"></div>
      <div class="item-error">Такая навигация уже существует!</div>
    </label>
    @if ($navigation->moderation == 1)
    <div class="checkbox">
      {{ Form::checkbox('moderation', 1, $navigation->moderation, ['id' => 'moderation']) }}
      <label for="moderation"><span>Временная запись.</span></label>
    </div>
    @endif
    @can('god', App\Navigation::class)
    <div class="checkbox">
      {{ Form::checkbox('system_item', 1, $navigation->system_item, ['id' => 'system-item']) }}
      <label for="system-item"><span>Системная запись.</span></label>
    </div>
    @endcan
    {{ Form::hidden('navigation_id', $navigation->id, ['id' => 'navigation-id']) }}
    {{ Form::hidden('site_id', $site->id) }}
    {{ Form::hidden('first_item', 0, ['class' => 'first-item']) }}
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button', 'id'=>$id]) }}
  </div>
</div>