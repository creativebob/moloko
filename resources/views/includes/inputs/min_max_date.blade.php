<fieldset>
    <legend>{{ $title ?? 'Дата' }}</legend>

    <div class="grid-x grid-margin-x">

        @php
            $nameMin = "{$name}_min";
            $nameMax = "{$name}_max";
        @endphp

        <div class="cell small-6">
            <label>от:
                <pickmeup-component
                    name="{{ $nameMin }}"
                    @isset(request()->$nameMin)
                        value="{{ \Carbon\Carbon::createFromFormat('d.m.Y', request()->$nameMin) }}"
                    @endisset
                ></pickmeup-component>
            </label>
        </div>

        <div class="cell small-6">
            <label>до:
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
