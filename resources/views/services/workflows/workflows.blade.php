{{-- Подключаем класс для работы с составами --}}
@include('services.workflows.class')

<div class="grid-x grid-padding-x">
	<div class="small-12 medium-9 cell">
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


<script type="text/javascript">

	let workflows = new Workflows();

	// Чекбоксы
	$(document).on('click', "#dropdown-workflows :checkbox", function() {
		workflows.change(this);
	});

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-workflows a[data-open=\"delete-item\"]", function() {
		workflows.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		workflows.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });
</script>