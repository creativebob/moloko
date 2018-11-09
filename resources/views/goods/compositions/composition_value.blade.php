
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
		@if (isset($composition->goods_product_id))
		{{ $composition->pivot->value . ' ' . $composition->goods_product->unit->abbreviation }}
		@else
		{{ $composition->pivot->value . ' ' . $composition->raws_product->unit->abbreviation }}
		@endif
	</td>
	<td>
		<div class="wrap-input-table">
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
		</div>
	</td>
	<td>
		<div class="wrap-input-table">
		</div>
	</td>
	<td>
	</td>

	<td class="td-delete">
	</td>
</tr>
