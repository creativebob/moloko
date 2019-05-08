{{-- Подключаем класс для работы с составами --}}
@include('services_categories.workflows.class')

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
			<tbody id="table-workflows">

				@if ($category->workflows->isNotEmpty())
				@foreach ($category->workflows as $workflow)
				@include ('services_categories.workflows.workflow_tr', $workflow)
				@endforeach
				@endif

			</tbody>
		</table>
	</div>

	<div class="small-12 medium-3 cell">

		<ul class="menu vertical">
			<li>
				<a class="button" data-toggle="dropdown-workflows">Рабочие процессы</a>
				<div class="dropdown-pane" id="dropdown-workflows" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

					<ul class="checker" id="categories-list">

						@include('services_categories.workflows.workflows_list')
					</ul>

				</div>
			</li>
		</ul>
	</div>
</div>