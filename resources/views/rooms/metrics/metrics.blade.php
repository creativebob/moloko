<fieldset class="fieldset-access">
    <legend>Метрики</legend>
    <div id="metrics-list">

        <label>
            <span data-tooltip tabindex="1" title="">Площадь (м2)</span>
            {{ Form::number('area', isset($item->area) ? $item->area: null, ['required', $disabled ? 'disabled' : '']) }}
            <span class="form-error">Поле обязательно для заполнения!</span>
        </label>

        @include('includes.scripts.class.city_search')

        <div class="grid-x grid-margin-x">
            <div class="small-12 medium-4 cell">
                {{-- Город --}}
                @include('includes.inputs.city_search', ['city' => isset($item->location->city->name) ? $item->location->city : null, 'id' => 'cityForm', 'required' => true])
            </div>
            <div class="small-12 medium-8 cell">
                <label>Адрес
                    @include('includes.inputs.address', ['value' => isset($item->location->address) ? $item->location->address : null, 'name' => 'address'])
                </label>
            </div>
        </div>

    </div>
</fieldset>



