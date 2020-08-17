{{-- Чекбокс отображения на сайте --}}
{!! Form::hidden('display', 0) !!}
@can ('display', $item)
<div class="small-12 cell checkbox">
	{!! Form::checkbox('display', 1, $item->display, ['id' => 'checkbox-display']) !!}
	<label for="checkbox-display"><span>Отображать на сайте</span></label>
</div>
@endcan

{{-- Чекбокс модерации --}}
{!! Form::hidden('moderation', 0) !!}
@can ('moderator', $item)
@moderation ($item)
<div class="small-12 cell checkbox">
	{!! Form::checkbox('moderation', 1, $item->moderation, ['id' => 'checkbox-moderation']) !!}
	<label for="checkbox-moderation"><span>Временная запись.</span></label>
</div>
@endmoderation
@endcan

{{-- Чекбокс системной записи --}}
{!! Form::hidden('system', 0) !!}
@can ('system', $item)
<div class="small-12 cell checkbox">
	{!! Form::checkbox('system', 1, $item->system, ['id' => 'checkbox-system']) !!}
	<label for="checkbox-system"><span>Сделать запись системной</span></label>
</div>
@endcan
