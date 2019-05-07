{{-- Подключаем класс для работы с составами --}}
@include('goods_categories.compositions.class')
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
			<tbody id="compositions-table">

				@if ($category->compositions->isNotEmpty())
				@foreach ($category->compositions as $composition)
				@include ('goods_categories.compositions.composition_tr', $composition)
				@endforeach
				@endif

			</tbody>
		</table>
	</div>

	<div class="small-12 medium-3 cell">

		<ul class="menu vertical">
			<li>
				<a class="button" data-toggle="compositions-dropdown">Состав</a>
				<div class="dropdown-pane" id="compositions-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

					<ul class="checker" id="categories-list">

						@include('goods_categories.compositions.compositions_list')
					</ul>

				</div>
			</li>
		</ul>
	</div>
</div>