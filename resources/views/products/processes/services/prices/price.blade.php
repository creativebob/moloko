<tr class="item" id="prices_service-{{ $prices_service->id }}" data-name="{{ $prices_service->catalogs_item->name }}" data-filial_id="{{ $prices_service->filial_id }}" data-catalogs_item_id="{{ $prices_service->catalogs_services_item_id }}" data-catalog_id="{{ $prices_service->catalogs_service_id }}">

	<td>{{ $prices_service->catalog->name }}</td>
	<td>{{ $prices_service->catalogs_item->name }}</td>
	<td>{{ $prices_service->filial->name ?? 'Общая' }}</td>
	<td class="price">
		@include('products.processes.services.prices.catalogs_item_price', ['price' => $prices_service->price])
	</td>

	<td class="td-delete">
		<a class="icon-delete sprite" data-open="delete-price"></a>
	</td>
</tr>
