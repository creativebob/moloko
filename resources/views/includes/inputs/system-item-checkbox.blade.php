{{-- Чекбокс системной записи --}}
@can ('god', $user)
  <div class="small-12 cell checkbox">
    {{ Form::checkbox('system_item', 1, $user->system_item, ['id'=>'system-checkbox']) }}
    <label for="system-checkbox"><span>Сделать запись системной.</span></label>
  </div>
@endcan