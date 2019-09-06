<div class="grid-x">
	<div class="cell small-12">
		<h4>По цене, руб.</h4>
		<div class="grid-x padding-light">
			<div class="cell small-6">
				<label for="sliderOutput1" class="ininput">от:</label>
				<input type="number" class="input-text" id="sliderOutput1" name="price[min]">
			</div>
			<div class="cell small-6">
				<label for="sliderOutput2" class="ininput">до:</label>
				<input type="number" id="sliderOutput2" class="input-text" name="price[max]">
			</div>
		</div>
	</div>
    <div class="cell small-12">
		<div
                class="slider"
                data-slider
				data-initial-start="{{ isset($request->price) ? $request->price['min'] : $price['min'] }}"
                data-initial-end="{{ isset($request->price) ? $request->price['max'] : $price['max'] }}"
                data-start="{{ ($price['max'] == $price['min']) ? $price['min'] - 1 : $price['min'] }}"
                data-end="{{ $price['max'] }}"
                {{-- data-step="{{ $price['step'] }}" --}}
        >
			<span class="slider-handle" data-slider-handle role="slider" tabindex="1" aria-controls="sliderOutput1"></span>
			<span class="slider-fill" data-slider-fill></span>
			<span class="slider-handle" data-slider-handle role="slider" tabindex="1" aria-controls="sliderOutput2"></span>
		</div>
	</div>
</div>