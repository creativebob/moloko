{{-- Подключаем класс для работы с составами --}}
@include('products.processes.services.workflows.class')

@if ($workflows_categories->isNotEmpty())

@foreach($workflows_categories as $workflows_category)

@if ($workflows_category->workflows->isNotEmpty())
<li>
	<span class="parent" data-open="workflow_category-{{ $workflows_category->id }}">{{ $workflows_category->name }}</span>
	<div class="checker-nested" id="workflow_category-{{ $workflows_category->id }}">
		<ul class="checker">

			@foreach($workflows_category->workflows as $workflow)
			<li class="checkbox">
				{{ Form::checkbox(null, $workflow->id, in_array($workflow->id, $process->workflows->pluck('id')->toArray()), ['class' => 'add-workflow', 'id' => 'workflow-'.$workflow->id]) }}
				<label for="workflow-{{ $workflow->id }}">
					<span>{{ $workflow->process->name }}</span>
				</label>
			</li>
			@endforeach

		</ul>
	</div>
</li>
@endif

@endforeach

@else
<li>Ничего нет...</li>
@endif

<script type="text/javascript">

	let workflows = new Workflows();

	// Чекбоксы
	$(document).on('click', "#dropdown-workflows :checkbox", function() {
		workflows.change(this);
	});

	// Удаление со страницы
	// Открываем модалку
	$(document).on('click', "#table-workflows a[data-open=\"delete-item\"]", function() {
		workflows.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		workflows.delete(id);
	});

    // При клике на свойство отображаем или скрываем
    $(document).on('click', '.parent', function() {
        // Скрываем все
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });
</script>



