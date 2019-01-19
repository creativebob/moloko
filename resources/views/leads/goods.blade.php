<tr class="item" id="order_compositions-{{ $composition->id }}" data-name="{{ $composition->product->goods_article->name }}">
	<td>{{ $composition->product->goods_article->name }}</td>
	<td>{{ $composition->count }}</td>
<!-- 	<td>{{ num_format($composition->product->cost, 0) }}</td> -->
<!-- 	<td><input type="text"></td>
	<td><input type="text"></td> -->
	<td><a class="button green-button" data-open="price-set">{{ num_format($composition->product->price, 0) }}</a></td>
	<td class="actions">
		<div class="icon-delete sprite" data-open="item-delete-ajax"></div>
	</td>
</tr>
















