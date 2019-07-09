{{-- Подключаем класс для работы с метриками --}}
@include('includes.category_metrics.class')

<div class="grid-x grid-padding-x">
	<div class="small-12 medium-8 cell">
		<table>
			<thead>
				<tr>
					<th>Название</th>
					<th>Минимум</th>
					<th>Максимум</th>
					<th>Подтверждение</th>
					<th>Отрицание</th>
					<th>Цвет</th>
					<th>Список</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="table-metrics">
				{{-- Таблица метрик товара --}}

				@if ($category->metrics->isNotEmpty())
				@foreach ($category->metrics as $metric)
				@include('includes.category_metrics.metric', $metric)
				@endforeach
				@endif

			</tbody>
		</table>
	</div>
	<div class="small-12 medium-4 cell">

		{{-- {{ Form::open(['url' => '/add_category_metric', 'id' => 'properties-form', 'data-abide', 'novalidate']) }} --}}
		<div id="properties-form">
		<fieldset>
			<legend><a data-toggle="properties-dropdown">Добавить метрику</a></legend>
			<div class="grid-x grid-padding-x" id="property-form"></div>
		</fieldset>
		{{ Form::hidden('entity_id', $category->id) }}
		</div>
		{{-- {{ Form::close() }} --}}

		{{-- Список свойств с метриками --}}
		<div class="dropdown-pane properties-dropdown" id="properties-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

			{{-- @include('includes.category_metrics.properties_form') --}}
			@include('includes.category_metrics.properties_list')

		</div>
	</div>
</div>

<script type="application/javascript">

	let metrics = new Metrics("{{ $category->getTable() }}", "{{ $category->id }}");

	// Чекбоксы
	$(document).on('click', "#properties-dropdown :checkbox", function() {
		metrics.change(this);
	});

	$(document).on('change', "#properties-dropdown #properties-select", function() {
		metrics.getForm(this);
	});

	// При клике на кнопку под Select'ом свойств
	$(document).on('click', '#properties-form #add-metric', function(event) {
		event.preventDefault();
		metrics.addMetric();
	});

	// При клике на кнопку добавления значений к метрике
	$(document).on('click', '#properties-form .add-value', function(event) {
		event.preventDefault();
		metrics.addMetricValue();
	});

	// При клике на кнопку удаления удаляем значение метрики
	$(document).on('click', "#properties-form a[data-open=\"delete-metric-value\"]", function() {
		metrics.deleteMetricValue(this);
	});

	// Удаление метрики со страницы
	// Открываем модалку
	$(document).on('click', "#table-metrics a[data-open=\"delete-metric\"]", function() {
		metrics.openModal(this);
	});
	// Удаляем
	$(document).on('click', '.metric-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		metrics.deleteMetric(id);
	});


    // При клике на свойство отображаем или скрываем его метрики
    $(document).on('click', '.parent', function() {
        // Скрываем все метрики
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });



</script>