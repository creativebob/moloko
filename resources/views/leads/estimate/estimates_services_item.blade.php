<tr class="item" id="estimates_services_items-{{ $estimates_services_item->id }}" data-name="{{ $estimates_services_item->product->process->name }}" data-price_id="{{ $estimates_services_item->price_id }}">
	<td>{{ $estimates_services_item->product->process->name }}</td>
	<td>{{ $estimates_services_item->count }}</td>
	<td><a class="button green-button" data-open="price-set">{{ num_format($estimates_services_item->amount, 0) }}</a></td>
	<td class="actions">
		<div class="icon-delete sprite" data-open="delete-estimates_item"></div>
	</td>
</tr>
















