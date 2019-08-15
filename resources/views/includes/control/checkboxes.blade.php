{{-- Чекбокс отображения на сайте --}}
{!! Form::hidden('display', 0) !!}
@can ('display', $item)
<div class="small-12 cell checkbox">
	{!! Form::checkbox('display', 1, $item->display, ['id' => 'display-checkbox']) !!}
	<label for="display-checkbox"><span>Отображать на сайте</span></label>
</div>
@endcan

{{-- Чекбокс модерации --}}
{!! Form::hidden('moderation', 0) !!}
@can ('moderator', $item)
@moderation ($item)
<div class="small-12 cell checkbox">
	{!! Form::checkbox('moderation', 1, $item->moderation, ['id'=>'moderation-checkbox']) !!}
	<label for="moderation-checkbox"><span>Временная запись.</span></label>
</div>
@endmoderation
@endcan

{{-- Чекбокс системной записи --}}
{!! Form::hidden('system', 0) !!}
@can ('system', $item)
<div class="small-12 cell checkbox">
	{!! Form::checkbox('system', 1, $item->system, ['id'=>'system-item-checkbox']) !!}
	<label for="system-item-checkbox"><span>Сделать запись системной.</span></label>
</div>
@endcan
