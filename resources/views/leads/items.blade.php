{{-- Если вложенный --}}

@foreach ($goods_list as $cur_goods)
<li><a class="add-to-order" id="{{ $entity }}-{{ $cur_goods->id }}">

	<div class="media-object stack-for-small">
	  <div class="media-object-section items-product-img" >
	    <div class="thumbnail">
			<img
			@if(isset($cur_goods->photo_id)) 
				src="/storage/{{ $cur_goods->company_id }}/media/goods/{{ $cur_goods->id }}/img/small/{{ $cur_goods->photo->name }}" 
			@else 
				src="/crm/img/plug/goods_small_default_color.jpg" 
			@endif
			>
	    </div>
	  </div>
	  <div class="media-object-section">
	    <h4><span class="items-product-name">{{ $cur_goods->goods_article->name }}</span></h4>
		<p class="items-product-description">{{ $cur_goods->description }}</p>
		<span class="items-product-price">{{ $cur_goods->price }}</span>
	  </div>
	</div>

</a></li>
@endforeach
















