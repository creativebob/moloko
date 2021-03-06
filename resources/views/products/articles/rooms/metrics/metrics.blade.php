<fieldset class="fieldset-access">
    <legend>Метрики</legend>
    <div id="metrics-list">

        <label>
            <span data-tooltip tabindex="1" title="Площадь помещения">Площадь (м2)</span>
            {{ Form::number('area', isset($item->area) ? $item->area: null, [$disabled ? 'disabled' : '']) }}
            <span class="form-error">Поле обязательно для заполнения!</span>
        </label>


        <div class="grid-x grid-margin-x">
            <div class="small-12 medium-4 cell">
                {{-- Город --}}
                @include('system.common.includes.city_search', ['item' => $item, 'required' => true])
            </div>
            <div class="small-12 medium-8 cell">
                <label>Адрес
                    @include('includes.inputs.address', ['value' => isset($item->location->address) ? $item->location->address : null, 'name' => 'address'])
                </label>
            </div>
        </div>

    </div>
</fieldset>