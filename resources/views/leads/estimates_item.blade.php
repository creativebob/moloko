<tr class="item" id="etimates_item-{{ $etimates_item->id }}" data-name="{{ $etimates_item->product->article->name }}">
	<td>{{ $etimates_item->product->article->name }}</td>
	<td>{{ $etimates_item->count }}</td>
	<td><a class="button green-button" data-open="price-set">{{ num_format($etimates_item->product->price, 0) }}</a></td>
	<td class="actions">
		<div class="icon-delete sprite" data-open="item-delete-ajax"></div>
	</td>
</tr>
















