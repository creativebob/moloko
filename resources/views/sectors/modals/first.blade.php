<div class="grid-x grid-padding-x align-center modal-content inputs">
  <div class="small-10 cell">
    <label>Название индустрии
      @include('includes.inputs.name', ['value'=>$sector->name, 'name'=>'name', 'required'=>'required'])
      <div class="item-error">Такая индустрия уже существует!</div>
    </label>
    {{ Form::hidden('first_item', 0, ['class' => 'first-item', 'pattern' => '[0-9]{1}']) }}
    {{ Form::hidden('sector_id', $sector->id, ['id' => 'sector-id']) }}
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
    {{ Form::submit('Сохранить', ['data-close', 'class'=>'button modal-button '.$class]) }}
  </div>
</div>
