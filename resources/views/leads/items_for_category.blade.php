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

			<div class="media-object-section">
				<h4>
					<span class="items-product-name">{{ $item->article->name }}</span>
				</h4>
				<p class="items-product-description">{{ $item->description }}</p>
				<span class="items-product-price">{{ $item->price }}</span>
			</div>
		</div>

	</a>
</li>
@endforeach
















