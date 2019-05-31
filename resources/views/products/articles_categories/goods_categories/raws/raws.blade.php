<div class="grid-x grid-padding-x">
	<div class="small-12 medium-9 cell">
		<table class="table-compositions">

			<thead>
				<tr>
					<th>Категория</th>
					<th>Название</th>
					<th>Описание</th>
					<th>Ед. изм.</th>
					<th></th>
				</tr>
			</thead>

			<tbody id="table-raws">

				@if ($category->raws->isNotEmpty())
				@foreach ($category->raws as $raw)
				@include ('products.articles_categories.goods_categories.raws.raw_tr', $raw)
				@endforeach
				@endif

			</tbody>
		</table>
	</div>

	<div class="small-12 medium-3 cell">
		<ul class="menu vertical">
			<li>
				<a class="button" data-toggle="dropdown-raws">Сырье</a>
				<div class="dropdown-pane" id="dropdown-raws" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

					<ul class="checker" id="categories-list">

						@include('products.articles_categories.goods_categories.raws.raws_list')
					</ul>

				</div>
			</li>
		</ul>
	</div>
</div>
