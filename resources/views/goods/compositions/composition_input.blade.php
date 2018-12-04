
<tr class="item" id="compositions-{{ $composition->id }}" data-name="{{ $composition->name }}">
	<td>
		@if (isset($composition->goods_product_id))
		{{ $composition->goods_product->goods_category->name }}
		@else
		{{ $composition->raws_product->raws_category->name }}
		@endif
	</td>
	<td>{{ $composition->name }}</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('compositions_values['.$composition->id.']', $composition->pivot ? $composition->pivot->value : null, ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'1', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="1" class="text-to-placeholder">
				@if (isset($composition->goods_product_id))
				{{ $composition->goods_product->unit->abbreviation }}
				@else
				{{ $composition->raws_product->unit->abbreviation }}
				@endif
			.</label>
			<div class="sprite-input-right find-status" id="name-check"></div>
			<span class="form-error">Введите количество</span>
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
			{{-- Количество чего-либо --}}
			{{ Form::text('raw_count', '', ['class'=>'digit-field name-field compact w12 padding-to-placeholder', 'id'=>'2', 'maxlength'=>'7', 'autocomplete'=>'off', 'pattern'=>'[0-9\W\s]{0,10}', 'placeholder'=>'0', !empty($disabled) ? 'disabled' : '']) }}
			<label for="2" class="text-to-placeholder">
				@if (isset($composition->goods_product_id))
				{{ $composition->goods_product->unit->abbreviation }}
				@else
				{{ $composition->raws_product->unit->abbreviation }}
				@endif
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
				@if (isset($composition->goods_product_id))
				{{ $composition->goods_product->unit->abbreviation }}
				@else
				{{ $composition->raws_product->unit->abbreviation }}
				@endif
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
				@if (isset($composition->goods_product_id))
				{{ $composition->goods_product->unit->abbreviation }}
				@else
				{{ $composition->raws_product->unit->abbreviation }}
				@endif
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
