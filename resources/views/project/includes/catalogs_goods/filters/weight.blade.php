<div class="grid-x">
    <div class="cell small-12">
        <h4>По весу, гр.</h4>
        <div class="grid-x padding-light">
            <div class="cell small-6">
                <label for="sliderOutput3" class="ininput">от:</label>
                <input type="number" class="input-text" id="sliderOutput3" name="weight[min]">
            </div>
            <div class="cell small-6">
                <label for="sliderOutput4" class="ininput">до:</label>
                <input type="number" id="sliderOutput4" class="input-text" name="weight[max]">
            </div>
        </div>
    </div>
    <div class="cell small-12">
        <div
            class="slider"
            data-slider
            data-initial-start="{{ request()->get("weight['min']", $weight['min']) }}"
            data-initial-end="{{ request()->get("weight['max']", $weight['max']) }}"
            data-start="{{ ($weight['max'] == $weight['min']) ? $weight['min'] - 1 : $weight['min'] }}"
            data-end="{{ $weight['max'] }}"
            {{-- data-step="{{ $weight['step'] }}" --}}
        >
            <span class="slider-handle" data-slider-handle role="slider" tabindex="1" aria-controls="sliderOutput3"></span>
            <span class="slider-fill" data-slider-fill></span>
            <span class="slider-handle" data-slider-handle role="slider" tabindex="1" aria-controls="sliderOutput4"></span>
        </div>
    </div>
</div>
