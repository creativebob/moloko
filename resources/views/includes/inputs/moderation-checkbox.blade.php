{{-- Чекбокс модерации --}}
@can ('moderator', $item)
  @if ($item->moderation == 1)
    <div class="small-12 cell checkbox">
      {{ Form::checkbox('moderation', 1, $item->moderation, ['id'=>'moderation-checkbox']) }}
      <label for="moderation-checkbox"><span>Временная запись.</span></label>
    </div>
  @endif
@endcan