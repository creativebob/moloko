<div class="grid-x grid-padding-x">
	<div class="small-12 medium-9 cell">

		<div class="grid-x grid-padding-x">
			<div class="small-12 medium-6 large-9 cell">
			</div>
			<div class="small-12 medium-6 large-3 cell">
				{{-- Если статус у товара статус черновика, то показываем сырье --}}
				@if ($article->draft)
				<ul class="menu vertical">
					<li>
						<a class="button" data-toggle="dropdown-raws">Добавить сырье</a>
						<div class="dropdown-pane" id="dropdown-raws" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

							<ul class="checker" id="categories-list">
								@include('products.articles.goods.raws.raws_list', ['article' => $article])
							</ul>

						</div>
					</li>
				</ul>
				@endif
			</div>
		</div>

		<div class="small-12 cell">

			{{-- Состав --}}
			<table class="table-compositions">

				<thead>
					<tr>
						<th>Категория:</th>
						<th>Продукт:</th>
						<th>Кол-во:</th>
						<th>Использование:</th>
						<th>Отход:</th>
						<th>Остаток:</th>
						<th>Операция над остатком:</th>
						<th></th>
					</tr>
				</thead>

				<tbody id="table-raws">

					@if ($article->raws->isNotEmpty())
					@foreach ($article->raws as $raw)
					@include ('products.articles.goods.raws.raw_input', $raw)
					@endforeach
					@endif

				</tbody>
			</table>
		</div>
	</div>
<div class="small-12 medium-3 cell">



	</div>
</div>
