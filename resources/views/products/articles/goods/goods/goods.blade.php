<div class="grid-x grid-padding-x">
	<div class="small-12 medium-9 cell">
		{{-- Состав --}}
		<table class="table-compositions">

			<thead>
				<tr>
					<th>Категория:</th>
					<th>Продукт:</th>
					<th>Кол-во:</th>
					<th></th>
				</tr>
			</thead>

			<tbody id="table-goods">

				@if ($article->goods->isNotEmpty())
				@foreach ($article->goods as $cur_goods)
				@include ('products.articles.goods.goods.goods_input', $cur_goods)
				@endforeach
				@endif

			</tbody>
		</table>
	</div>

	<div class="small-12 medium-3 cell">

		{{-- Если статус у товара статус черновика, то показываем сырье --}}
		@if ($article->draft)
		<ul class="menu vertical">
			<li>
				<a class="button" data-toggle="dropdown-goods">Состав</a>
				<div class="dropdown-pane" id="dropdown-goods" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

					<ul class="checker" id="categories-list">
						@include('products.articles.goods.goods.goods_list', ['article' => $article])
					</ul>

				</div>
			</li>
		</ul>
		@endif

	</div>
</div>
