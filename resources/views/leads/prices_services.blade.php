{{-- Если вложенный --}}
@if ($catalogs_services_item->prices_services->isNotEmpty())
@foreach ($catalogs_services_item->prices_services as $prices_service)
<li>
	<a class="add-to-estimate" id="prices_services-{{ $prices_service->id }}" data-serial="{{ $prices_service->service->serial }}">

		<div class="media-object stack-for-small">
			<div class="media-object-section items-product-img" >
				<div class="thumbnail">
					<img src="{{ getPhotoPath($prices_service->service->process, 'small') }}">
				</div>
			</div>

			<div class="media-object-section cell">

				<div class="grid-x grid-margin-x">
					<div class="cell auto">
						<h4>
							<span class="items-product-name">{{ $prices_service->service->process->name }}</span>
							@if($prices_service->service->process->manufacturer)
							<span class="items-product-manufacturer"> ({{ $prices_service->service->process->manufacturer->name ?? '' }})</span>
							@endif
						</h4>	
					</div>

					<div class="cell shrink wrap-product-price">

					<span class="items-product-price">{{ num_format($prices_service->price, 0) }}</span>
					</div>
				</div>
				<p class="items-product-description">{{ $prices_service->service->description }}</p>	
			</div>
		</div>

	</a>
</li>
@endforeach

@else

<li>Нет услуг</li>

@endif
















