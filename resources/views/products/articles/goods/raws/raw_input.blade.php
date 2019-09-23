
<tr class="item goods-composition-row" id="table-raws-{{ $raw->id }}" data-name="{{ $raw->article->name }}">
	<td class="number_counter"></td>
	<td>

		{{ $raw->category->name }}

	</td>
	<td>{{ $raw->article->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- КОЛИЧЕСТВО --}}
			{{ Form::text('raws['.$raw->id.'][value]', $raw->pivot ? $raw->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder raw-value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">
				{{ $raw->portion_goods_abbreviation ?? $raw->unit_for_composition->abbreviation ?? $raw->article->group->unit->abbreviation }}
			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			{{-- <span class="form-error">Введите количество</span> --}}
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- ИСПОЛЬЗОВАНИЕ --}}
			{{ Form::text('raws['.$raw->id.'][use]', $raw->pivot ? $raw->pivot->use : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder raw-use', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">

				{{ $raw->portion_goods_abbreviation ?? $raw->unit_for_composition->abbreviation ?? $raw->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
		</div>
	</td>

	{{-- <td>
		<div class="wrap-input-table">

			{{ Form::text('raws['.$raw->id.'][waste]', $raw->pivot ? $raw->pivot->waste : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder raw-waste', 'id'=>'3', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="3" class="text-to-placeholder">

				{{ $raw->portion_goods_abbreviation ?? $raw->unit_for_composition->abbreviation ?? $raw->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">

			{{ Form::text('raws['.$raw->id.'][leftover]', $raw->pivot ? $raw->pivot->leftover : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder raw-leftover', 'id'=>'4', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="4" class="text-to-placeholder">

				{{ $raw->portion_goods_abbreviation ?? $raw->unit_for_composition->abbreviation ?? $raw->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
		</div>
	</td>
	<td>
		@include('products.articles.goods.raws.leftover_operations_select', ['selected' => $raw->pivot ? $raw->pivot->leftover_operation_id : null])
	</td> --}}

	<td>
		@php if(isset($raw->pivot->use)){$count = $raw->pivot->use;} else {$count = 0;}; @endphp
		@if($raw->portion_goods_status)
			<input style="display: none;" class="raw-weight" value="{{ $raw->portion_goods_count * $raw->unit_portion_goods->ratio * 1000 }}">
			<span class="raw-weight-count">{{ num_format($raw->portion_goods_count * $raw->unit_portion_goods->ratio * 1000 * $count, 0) }}</span>
		@else
			@if($raw->article->unit_id == 32)
				<input style="display: none;" class="raw-weight" value="{{ $raw->article->weight * $raw->unit_weight->ratio * 1000 }}">
				<span class="raw-weight-count">{{ num_format($raw->article->weight * $raw->unit_weight->ratio * 1000 * $count, 0)}}</span>
			@else
				<input style="display: none;" class="raw-weight" value="{{ 1000 }}">
				<span class="raw-weight-count">{{ num_format(1000 * $count, 0)}}</span>
			@endif
		@endif
		<span>гр.</span>
	</td>
	<td class="td-delete">
		@empty($disabled)
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endempty
	</td>
</tr>
