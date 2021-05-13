
@if($serviceFlow->process->prices->isNotEmpty())
    <span class="price">{{ num_format($serviceFlow->process->prices->first()->price, 0) }}  ₽ / чел.</span>
@endif

<label>Выберите дату тура:
	<select class="select-service-flow">
		<option>12 мая - 20 мая</option>
		<option>01 июнь - 18 июнь</option>
		<option>27 июль - 03 июль</option>
	</select>
</label>

<div class="wrap-button-center">
	<a href="#" class="button" data-open="modal-call">Бронировать</a>
</div>
