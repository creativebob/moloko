<fieldset>
    <legend>{{ $title ?? 'Дата' }}</legend>

    <div class="grid-x grid-margin-x">

        <div class="cell small-6">
            @php
                $nameMin = "{$name}_min";
                $nameMax = "{$name}_max";
            @endphp

            <label class="ininput">от:
                <pickmeup-component
                    name="{{ $nameMin }}"
                    @isset(request()->$nameMin)
                    value="{{ \Carbon\Carbon::createFromFormat('d.m.Y', request()->$nameMin) }}"
                    @endisset
                ></pickmeup-component>
            </label>
        </div>

        <div class="cell small-6">
            <label class="ininput">до:
                <pickmeup-component
                    name="{{ $nameMax }}"
                    @isset(request()->$nameMax)
                    value="{{ \Carbon\Carbon::createFromFormat('d.m.Y', request()->$nameMax) }}"
                    @endisset
                ></pickmeup-component>
            </label>
        </div>

    </div>
</fieldset>
