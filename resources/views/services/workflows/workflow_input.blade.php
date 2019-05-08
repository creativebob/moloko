
<tr class="item" id="table-workflow-{{ $workflow->id }}" data-name="{{ $workflow->process->name }}">
	<td>

		{{ $workflow->category->name }}

	</td>
	<td>{{ $workflow->process->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('workflows['.$workflow->id.'][value]', $workflow->pivot ? $workflow->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder workflow_value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">

				{{ $workflow->process->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('workflows['.$workflow->id.'][use]', $workflow->pivot ? $workflow->pivot->use : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">

				{{ $workflow->process->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('workflows['.$workflow->id.'][waste]', $workflow->pivot ? $workflow->pivot->waste : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'3', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="3" class="text-to-placeholder">

				{{ $workflow->process->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('workflows['.$workflow->id.'][leftover]', $workflow->pivot ? $workflow->pivot->leftover : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'4', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="4" class="text-to-placeholder">

				{{ $workflow->process->group->unit->abbreviation }}

			.</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>


	<td class="td-delete">
		@if (empty($disabled))
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endif
	</td>
</tr>
