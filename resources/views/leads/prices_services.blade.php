{{-- Если вложенный --}}
@if ($catalogs_services_item->services->isNotEmpty())
@foreach ($catalogs_services_item->services as $service)
<li>
	<a class="add-to-estimate" id="prices_services-{{ $service->id }}" data-serial="{{ $service->serial }}">

		<div class="media-object stack-for-small">
			<div class="media-object-section items-product-img" >
				<div class="thumbnail">
					<img src="{{ getPhotoPath($service->process, 'small') }}">
				</div>
			</div>

			<div class="media-object-section cell">

				<div class="grid-x grid-margin-x">
					<div class="cell auto">
						<h4>
							<span class="items-product-name">{{ $service->process->name }}</span>
							@if($service->process->manufacturer)
							<span class="items-product-manufacturer"> ({{ $service->process->manufacturer->name ?? '' }})</span>
							@endif
						</h4>	
					</div>

					<div class="cell shrink wrap-product-price">

					<span class="items-product-price">{{ num_format($service->pivot->price, 0) }}</span>
					</div>
				</div>
				<p class="items-product-description">{{ $service->description }}</p>	
			</div>
		</div>

	</a>
</li>
@endforeach

@else

<li>Нет услуг</li>

@endif
















