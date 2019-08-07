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
						<a class="button" data-toggle="dropdown-containers">Добавить сырье</a>
						<div class="dropdown-pane" id="dropdown-containers" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

							<ul class="checker" id="categories-list">
								@include('products.articles.goods.containers.containers_list', ['article' => $article])
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

				<tbody id="table-containers">

					@if ($article->containers->isNotEmpty())
						@foreach ($article->containers as $container)
							@include ('products.articles.goods.containers.container_input', $container)
						@endforeach
					@endif

				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="small-12 medium-3 cell">


</div>

@push('scripts')
	@include('products.articles.goods.containers.scripts')
@endpush