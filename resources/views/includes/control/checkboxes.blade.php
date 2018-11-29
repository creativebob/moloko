{{-- Чекбокс отображения на сайте --}}
@can ('display', $item)
<div class="small-12 cell checkbox">
	{{ Form::checkbox('display', 1, null, ['id' => 'display-checkbox']) }}
	<label for="display-checkbox"><span>Отображать на сайте</span></label>
</div>
@endcan

{{-- Чекбокс модерации --}}
@can ('moderator', $item)
@moderation ($item)
<div class="small-12 cell checkbox">
	{{ Form::checkbox('moderation', 1, null, ['id'=>'moderation-checkbox']) }}
	<label for="moderation-checkbox"><span>Временная запись.</span></label>
</div>
@endmoderation
@endcan

{{-- Чекбокс системной записи --}}
@can ('system', $item)
<div class="small-12 cell checkbox">
	{{ Form::checkbox('system_item', 1, null, ['id'=>'system-item-checkbox']) }}
	<label for="system-item-checkbox"><span>Сделать запись системной.</span></label>
</div>
@endcan
