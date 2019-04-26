
<tr class="item" id="table-composition-{{ $composition->id }}" data-name="{{ $composition->article->name }}">
	<td>

		{{ $composition->article->name }}

	</td>
	<td>{{ $composition->article->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('compositions['.$composition->id.'][value]', $composition->pivot ? $composition->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder composition_value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">

				{{ $composition->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('raw_count', '', ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">

				{{ $composition->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('raw_count', '', ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'3', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="3" class="text-to-placeholder">

				{{ $composition->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('raw_count', '', ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'4', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="4" class="text-to-placeholder">

				{{ $composition->article->group->unit->abbreviation }}

			.</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		{{ Form::select('action', ['1' => 'Списать', '2' => 'Вернуть на склад', '3' => 'Создать новый товар'], 0, ['id' => 'units-list', 'class' => 'compact', !empty($disabled) ? 'disabled' : '']) }}
	</td>

	<td class="td-delete">
		@if (empty($disabled))
			<a class="icon-delete sprite" data-open="delete-composition"></a>
		@endif
	</td>
</tr>
