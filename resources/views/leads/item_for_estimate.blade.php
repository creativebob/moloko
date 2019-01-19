<tr class="item" id="workflows-{{ $workflow->id }}" data-name="{{ $workflow->product->article->name }}">
	<td>{{ $workflow->product->article->name }}</td>
	<td>{{ $workflow->count }}</td>
	<td><a class="button green-button" data-open="price-set">{{ num_format($workflow->product->price, 0) }}</a></td>
	<td class="actions">
		<div class="icon-delete sprite" data-open="item-delete-ajax"></div>
	</td>
</tr>
















