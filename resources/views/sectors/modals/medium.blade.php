<div class="grid-x grid-padding-x modal-content inputs">
  <div class="small-10 small-offset-1 cell">
    <label>Расположение
      <select name="parent_id">
        @php
          echo $sectors_list;
        @endphp
      </select>
    </label>
    <label>Название сектора
      @include('includes.inputs.name', ['value'=>$sector->name, 'name'=>'name', 'required'=>'required'])
      <div class="sprite-input-right find-status"></div>
      <div class="item-error">Такой сектор уже существует!</div>
    </label>
    {{ Form::hidden('sector_id', $sector->id, ['id' => 'sector-id']) }}
    {{ Form::hidden('medium_item', 0, ['class' => 'medium-item', 'pattern' => '[0-9]{1}']) }}
    @if ($sector->moderation == 1)
      <div class="checkbox">
        {{ Form::checkbox('moderation', 1, $sector->moderation, ['id' => 'moderation']) }}
        <label for="moderation"><span>Временная запись.</span></label>
      </div>
      @endif
      @can('god', App\Sector::class)
      <div class="checkbox">
        {{ Form::checkbox('system_item', 1, $sector->system_item, ['id' => 'system-item']) }}
        <label for="system-item"><span>Системная запись.</span></label>
      </div>
      @endcan
  </div>
</div>
<div class="grid-x align-center">
  <div class="small-6 medium-4 cell">
    {{ Form::submit($submitButtonText, ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>