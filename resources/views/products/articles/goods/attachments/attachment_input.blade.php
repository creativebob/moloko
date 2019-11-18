
<tr class="item" id="table-attachments-{{ $attachment->id }}" data-name="{{ $attachment->article->name }}">
	<td class="number_counter"></td>
	<td>

		{{ $attachment->category->name }}

	</td>
	<td>{{ $attachment->article->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('attachments['.$attachment->id.'][value]', $attachment->pivot ? $attachment->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder attachment-value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">
				{{ $attachment->portion_abbreviation ?? $attachment->unit_for_composition->abbreviation ?? $attachment->article->group->unit->abbreviation }}
			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('attachments['.$attachment->id.'][useful]', $attachment->pivot ? $attachment->pivot->useful : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder attachment-use', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">

				{{ $attachment->portion_abbreviation ?? $attachment->unit_for_composition->abbreviation ?? $attachment->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	{{-- <td>
		<div class="wrap-input-table">

			{{ Form::text('attachments['.$attachment->id.'][waste]', $attachment->pivot ? $attachment->pivot->waste : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder attachment-waste', 'id'=>'3', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="3" class="text-to-placeholder">

				{{ $attachment->portion_abbreviation ?? $attachment->unit_for_composition->abbreviation ?? $attachment->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">

			{{ Form::text('attachments['.$attachment->id.'][leftover]', $attachment->pivot ? $attachment->pivot->leftover : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder attachment-leftover', 'id'=>'4', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="4" class="text-to-placeholder">

				{{ $attachment->portion_abbreviation ?? $attachment->unit_for_composition->abbreviation ?? $attachment->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		@include('products.articles.goods.attachments.leftover_operations_select', ['selected' => $attachment->pivot ? $attachment->pivot->leftover_operation_id : null])
	</td> --}}

	<td>
		@php if(isset($attachment->pivot->useful)){$count = $attachment->pivot->useful;} else {$count = 0;}; @endphp
			<span class="attachment-weight-count" data-weight="{{ $attachment->weight * 1000 }}" data-weight-count="{{ $attachment->weight * 1000 * $count }}">{{ num_format($attachment->weight * 1000 * $count, 0) }}</span>
		<span>гр.</span>
	</td>
	<td>
		@php 
			if(isset($attachment->cost_unit)){$cost = $attachment->cost_unit;} else {$cost = 0;};
		@endphp
			<span class="attachment-cost-count" data-cost={{ $cost }} data-cost-count={{ $cost * $count }}>{{ num_format($cost * $count, 0) }}</span>
		<span>руб.</span>
	</td>

	<td class="td-delete">
		@empty($disabled)
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endempty
	</td>
</tr>
