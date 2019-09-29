<div class="grid-x grid-padding-x">
	<div class="small-12 medium-9 cell">

		<div class="grid-x grid-padding-x">
			<div class="small-12 medium-6 large-9 cell raw-indicators">
				<div class="grid-x grid-margin-x">
						<div class="cell shrink">
							<span class="indicator-name">Вес: </span><span class="total_count_weight">0</span> <span>гр.</span>
						</div>
						<div class="cell auto">
							<span class="indicator-name">Себестоимость: </span><span class="total_count_cost">0</span> <span>руб.</span>
						</div>
				</div>
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
						<th>п/п</th>
						<th>Категория:</th>
						<th>Продукт:</th>
						<th>Кол-во:</th>
						<th>Использование:</th>
						{{-- <th>Отход:</th>
						<th>Остаток:</th>
						<th>Операция над остатком:</th> --}}
						<th>Вес</th>
						<th>Себестоимость</th>
						<th></th>
					</tr>
				</thead>

				<tbody id="table-raws">

					@if ($article->raws->isNotEmpty())
						@foreach ($article->raws as $raw)
{{--							<rawcomposition-component :raw="{{ json_encode($raw) }}" :disabled="{{ $raw->article->draft }}"></rawcomposition-component>--}}
							@include ('products.articles.goods.raws.raw_input', $raw)
						@endforeach
					@endif

				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>
							<span class="total_count_weight">0</span> <span>гр.</span>
						</td>
						<td>
							<span class="total_count_cost">0</span> <span>руб.</span>
						</td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<div class="small-12 medium-3 cell">


</div>

@push('scripts')
	@include('products.articles.goods.raws.scripts')
@endpush