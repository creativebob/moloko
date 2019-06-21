@if ($groups->isNotEmpty())
<div class="small-12 cell up-input-button text-center">
	<a id="mode-default" class="modes">Вернуться</a>
</div>

<label>Группа товаров
	<select name="group_id" id="select-groups" required>

		@foreach ($groups as $group)
		<option value="{{ $group->id }}" data-abbreviation="{{ $group->unit->abbreviation }}">{{ $group->name }}</option>
		@endforeach

	</select>
</label>

@else

В данной категории нет групп, выберите другую категорию или <a id="mode-default" class="modes">вернитесь назад</a>

@endif

{{ Form::hidden('mode', 'mode-select', ['id' => 'mode']) }}
