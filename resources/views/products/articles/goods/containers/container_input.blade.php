
<tr class="item" id="table-containers-{{ $container->id }}" data-name="{{ $container->article->name }}">
	<td>

		{{ $container->category->name }}

	</td>
	<td>{{ $container->article->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('containers['.$container->id.'][value]', $container->pivot ? $container->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder container-value', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '', 'required']) }}
			<label for="1" class="text-to-placeholder">
				{{ $container->portion_goods_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}
			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('containers['.$container->id.'][use]', $container->pivot ? $container->pivot->use : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">

				{{ $container->portion_goods_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('containers['.$container->id.'][waste]', $container->pivot ? $container->pivot->waste : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'3', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="3" class="text-to-placeholder">

				{{ $container->portion_goods_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('containers['.$container->id.'][leftover]', $container->pivot ? $container->pivot->leftover : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'4', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="4" class="text-to-placeholder">

				{{ $container->portion_goods_abbreviation ?? $container->unit_for_composition->abbreviation ?? $container->article->group->unit->abbreviation }}

			</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		@include('products.articles.goods.containers.leftover_operations_select', ['selected' => $container->pivot ? $container->pivot->leftover_operation_id : null])
	</td>

	<td class="td-delete">
		@empty($disabled)
			<a class="icon-delete sprite" data-open="delete-item"></a>
		@endempty
	</td>
</tr>
