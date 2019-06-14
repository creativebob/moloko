<tr class="item" id="estimates_item-{{ $estimates_item->id }}" data-name="{{ $estimates_item->product->process->name }}" data-price="{{ $estimates_item->price_product_id }}">
	<td>{{ $estimates_item->product->process->name }}</td>
	<td>{{ $estimates_item->count }}</td>
	<td><a class="button green-button" data-open="price-set">{{ num_format($estimates_item->sum, 0) }}</a></td>
	<td class="actions">
		<div class="icon-delete sprite" data-open="item-delete-ajax"></div>
	</td>
</tr>
















