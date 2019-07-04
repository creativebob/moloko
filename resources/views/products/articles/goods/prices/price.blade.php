<tr class="item" id="cur_price_goods-{{ $cur_price_goods->id }}" data-name="{{ $cur_price_goods->catalogs_item->name }}" data-filial_id="{{ $cur_price_goods->filial_id }}" data-catalogs_item_id="{{ $cur_price_goods->catalogs_goods_item_id }}" data-catalog_id="{{ $cur_price_goods->catalogs_goods_id }}">

	<td>{{ $cur_price_goods->catalog->name }}</td>
	<td>{{ $cur_price_goods->catalogs_item->name }}</td>
	<td>{{ $cur_price_goods->filial->name ?? 'Общая' }}</td>
	<td class="price">
		@include('products.processes.services.prices.catalogs_item_price', ['price' => $cur_price_goods->price])
	</td>

	<td class="td-delete">
        @can('delete', $cur_price_goods)
        <a class="icon-delete sprite" data-open="delete-price"></a>
        @endcan
    </td>
</tr>
