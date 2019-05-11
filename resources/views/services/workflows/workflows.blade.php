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
			<tbody id="table-workflows">

				@if ($process->workflows->isNotEmpty())
				@foreach ($process->workflows as $workflow)
				@include ('services.workflows.workflow_input', $workflow)
				@endforeach
				@endif

			</tbody>
		</table>
	</div>

	<div class="small-12 medium-3 cell">

		{{-- Если статус у товара статус черновика, то показываем сырье --}}
		@if ($process->draft)
		<ul class="menu vertical">
			<li>
				<a class="button" data-toggle="dropdown-workflows">Состав</a>
				<div class="dropdown-pane" id="dropdown-workflows" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

					<ul class="checker" id="categories-list">
						@include('services.workflows.workflows_list', ['process' => $process])
					</ul>

				</div>
			</li>
		</ul>
		@endif

	</div>
</div>