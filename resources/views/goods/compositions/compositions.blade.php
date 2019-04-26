{{-- Состав --}}

{{-- Подключаем класс для работы с составами --}}
@include('goods.compositions.class')

<div class="tabs-panel" id="compositions">
	<div class="grid-x grid-padding-x">
		<div class="small-12 medium-9 cell">
			{{-- Состав --}}
			<table class="composition-table">
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
				<tbody id="table-compositions">

					@if ($article->compositions->isNotEmpty())
					@foreach ($article->compositions as $composition)
					@include ('goods.compositions.composition_input', $composition)
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
					<a class="button" data-toggle="compositions-dropdown">Состав</a>
					<div class="dropdown-pane" id="compositions-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

						<ul class="checker" id="categories-list">
							@include('goods.compositions.compositions_list', ['article' => $article])
						</ul>

					</div>
				</li>
			</ul>
			@endif

		</div>
	</div>
</div>