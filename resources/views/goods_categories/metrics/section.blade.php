@php
$metrics = ($set_status == 'one') ? 'one_metrics' : 'set_metrics';
@endphp

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
			<tbody id="{{ $set_status }}-metrics-table">
				{{-- Таблица метрик товара --}}

				@if (count($goods_category->$metrics))
				@foreach ($goods_category->$metrics as $metric)
				@include('goods_categories.metrics.metric', ['metric' => $metric, 'set_status' => $set_status])
				@endforeach
				@endif

			</tbody>
		</table>
	</div>
	<div class="small-12 medium-4 cell">

		{{ Form::open(['url' => '/add_goods_category_metric', 'id' => $set_status.'-properties-form', 'data-abide', 'novalidate']) }}
		<fieldset>
			<legend><a data-toggle="{{ $set_status }}-properties-dropdown">Добавить метрику</a></legend>
			<div class="grid-x grid-padding-x" id="{{ $set_status }}-property-form"></div>
		</fieldset>
		{{ Form::hidden('set_status', $set_status) }}
		{{ Form::hidden('entity_id', $goods_category->id) }}
		{{ Form::close() }}

		{{-- Список свойств с метриками --}}
		<div class="dropdown-pane" id="{{ $set_status }}-properties-dropdown" data-dropdown data-position="bottom" data-alignment="center" data-close-on-click="true">

			@include('goods_categories.metrics.properties_form', ['properties' => $properties, 'set_status' => $set_status])

		</div>
	</div>
</div>

<script type="text/javascript">

	let {{ $metrics }} = new Metrics("{{ $set_status }}", "goods_categories", "{{ $goods_category->id }}");

	// Таблица
	$(document).on('click', "#{{ $set_status }}-metrics-table a[data-open=\"delete-metric\"]", function() {
		{{ $metrics }}.openModal(this);
	});

	// Чекбоксы
	$(document).on('click', "#{{ $set_status }}-properties-dropdown :checkbox", function() {
		{{ $metrics }}.change(this);
	});

	$(document).on('change', "#{{ $set_status }}-properties-dropdown #{{ $set_status }}-properties-select", function() {
		{{ $metrics }}.getForm(this);
	});

	// При клике на кнопку под Select'ом свойств
	$(document).on('click', '#{{ $set_status }}-properties-form #add-metric', function(event) {
		event.preventDefault();
		{{ $metrics }}.addMetric();
	});

	// При клике на кнопку добавления значений к метрике
    $(document).on('click', '#{{ $set_status }}-properties-form .add-value', function(event) {
        event.preventDefault();
        {{ $metrics }}.addMetricValue();
    });


</script>