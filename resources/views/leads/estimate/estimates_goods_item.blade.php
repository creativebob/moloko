<tr class="item" id="estimates_goods_items-{{ $estimates_goods_item->id }}" data-name="{{ $estimates_goods_item->product->article->name }}" data-price_id="{{ $estimates_goods_item->price_id }}">
	<td>{{ $estimates_goods_item->product->article->name }}</td>
	<td>{{ $estimates_goods_item->count }}</td>
	<td><a class="button green-button" data-open="price-set">{{ num_format($estimates_goods_item->amount, 0) }}</a></td>
	<td class="actions">
		<div class="icon-delete sprite" data-open="delete-estimates_item"></div>
	</td>
</tr>
















