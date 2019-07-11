{{-- Подключаем класс для работы с составами --}}
@include('products.processes.services.services.class')

@if ($services_categories->isNotEmpty())

@foreach($services_categories as $services_category)

@if ($services_category->services->isNotEmpty())
<li>
	<span class="parent" data-open="service_category-{{ $services_category->id }}">{{ $services_category->name }}</span>
	<div class="checker-nested" id="service_category-{{ $services_category->id }}">
		<ul class="checker">

			@foreach($services_category->services as $service)
				@isset($service->process)
			<li class="checkbox">
				{{ Form::checkbox(null, $service->id, in_array($service->id, $process->services->pluck('id')->toArray()), ['class' => 'add-service', 'id' => 'service-'.$service->id]) }}
				<label for="service-{{ $service->id }}">
					<span>{{ $service->process->name }}</span>
				</label>
			</li>
				@endisset
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

	let services = new Services();

	// Чекбоксы
	$(document).on('click', "#dropdown-services :checkbox", function() {
		services.change(this);
	});

	// Удаление со страницы
	// Открываем модалку
	$(document).on('click', "#table-services a[data-open=\"delete-item\"]", function() {
		services.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		services.delete(id);
	});

    // При клике на свойство отображаем или скрываем
    $(document).on('click', '.parent', function() {
        // Скрываем все
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });
</script>



