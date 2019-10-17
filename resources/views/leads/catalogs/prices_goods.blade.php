{{-- Если вложенный --}}
@if ($catalogs_goods_item->prices_goods->isNotEmpty())
@foreach ($catalogs_goods_item->prices_goods as $cur_prices_goods)
<li>
	<a class="add-to-estimate" id="prices_goods-{{ $cur_prices_goods->id }}" data-serial="{{ $cur_prices_goods->goods->serial }}" data-type="goods">

		<div class="media-object stack-for-small">
			<div class="media-object-section items-product-img" >
				<div class="thumbnail">
					<img src="{{ getPhotoPath($cur_prices_goods->goods->article, 'small') }}">
				</div>
			</div>

			<div class="media-object-section cell">

				<div class="grid-x grid-margin-x">
					<div class="cell auto">
						<h4>
							<span class="items-product-name">{{ $cur_prices_goods->goods->article->name }}</span>
							@if($cur_prices_goods->goods->article->manufacturer)
							<span class="items-product-manufacturer"> ({{ $cur_prices_goods->goods->article->manufacturer->name ?? '' }})</span>
							@endif
						</h4>	
					</div>

					<div class="cell shrink wrap-product-price">

					<span class="items-product-price">{{ num_format($cur_prices_goods->price, 0) }}</span>
					</div>
				</div>
				<p class="items-product-description">{{ $cur_prices_goods->goods->description }}</p>	
			</div>
		</div>

	</a>
</li>
@endforeach

@else

<li>Нет товаров</li>

@endif
















