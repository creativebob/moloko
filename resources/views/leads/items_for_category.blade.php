{{-- Если вложенный --}}

@foreach ($items_list as $item)
<li>
	<a class="add-to-estimate" id="{{ $entity }}-{{ $item->id }}">

		<div class="media-object stack-for-small">
			<div class="media-object-section items-product-img" >
				<div class="thumbnail">
					<img src="{{ getPhotoPath($item, 'small') }}">
				</div>
			</div>

			<div class="media-object-section cell">

				<div class="grid-x grid-margin-x">
					<div class="cell auto">
						<h4>
							<span class="items-product-name">{{ $item->article->name }}</span>
							@if($item->article->manufacturer)
							<span class="items-product-manufacturer"> ({{ $item->article->manufacturer->name ?? '' }})</span>
							@endif
						</h4>	
					</div>

					<div class="cell shrink wrap-product-price">
					<span class="items-product-price">{{ num_format($item->price, 0) }}</span>
					</div>
				</div>
				<p class="items-product-description">{{ $item->description }}</p>	
			</div>
		</div>

	</a>
</li>
@endforeach
















