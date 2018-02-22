{{-- Чекбокс системной записи --}}
@can ('god', $item)
  <div class="small-12 cell checkbox">
    {{ Form::checkbox('system_item', 1, $item->system_item, ['id'=>'system-checkbox']) }}
    <label for="system-checkbox"><span>Сделать запись системной.</span></label>
  </div>
@endcan