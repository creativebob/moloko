
<tr class="item" id="table-containers-{{ $container->id }}" data-name="{{ $container->article->name }}">
	<td class="number_counter"></td>
	<td>

		{{ $container->category->name }}

	</td>
	<td>{{ $container->article->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('containers['.$container->id.'][value]', $container->pivot ? $container->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder container-value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">
				{{ $container->portion_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}
			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('containers['.$container->id.'][use]', $container->pivot ? $container->pivot->use : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder container-use', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">

				{{ $container->portion_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	{{-- <td>
		<div class="wrap-input-table">

			{{ Form::text('containers['.$container->id.'][waste]', $container->pivot ? $container->pivot->waste : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder container-waste', 'id'=>'3', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="3" class="text-to-placeholder">

				{{ $container->portion_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">

			{{ Form::text('containers['.$container->id.'][leftover]', $container->pivot ? $container->pivot->leftover : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder container-leftover', 'id'=>'4', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="4" class="text-to-placeholder">

				{{ $container->portion_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		@include('products.articles.goods.containers.leftover_operations_select', ['selected' => $container->pivot ? $container->pivot->leftover_operation_id : null])
	</td> --}}

	<td>
		@php if(isset($container->pivot->use)){$count = $container->pivot->use;} else {$count = 0;}; @endphp
			<span class="container-weight-count" data-weight="{{ $container->weight * 1000 }}" data-weight-count="{{ $container->weight * 1000 * $count }}">{{ num_format($container->weight * 1000 * $count, 0) }}</span>
		<span>гр.</span>
	</td>
	<td>
		@php 
			if(isset($container->cost_unit)){$cost = $container->cost_unit;} else {$cost = 0;};
		@endphp
			<span class="container-cost-count" data-cost={{ $cost }} data-cost-count={{ $cost * $count }}>{{ num_format($cost * $count, 0) }}</span>
		<span>руб.</span>
	</td>

	<td class="td-delete">
		@empty($disabled)
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endempty
	</td>
</tr>
