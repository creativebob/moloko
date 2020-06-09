<fieldset>
    <legend>{{ $title ?? 'Дата' }}</legend>

    <div class="grid-x grid-margin-x">

        @php
            $nameMin = "{$name}_min";
            $nameMax = "{$name}_max";
        @endphp

        <div class="cell small-6">
            <pickmeup-component
                name="{{ $nameMin }}"
                title="от:"
                @isset(request()->$nameMin)
                value="{{ \Carbon\Carbon::createFromFormat('d.m.Y', request()->$nameMin) }}"
                @endisset
            ></pickmeup-component>
        </div>

        <div class="cell small-6">
            <pickmeup-component
                name="{{ $nameMax }}"
                title="до:"
                @isset(request()->$nameMax)
                value="{{ \Carbon\Carbon::createFromFormat('d.m.Y', request()->$nameMax) }}"
                @endisset
            ></pickmeup-component>
        </div>

    </div>
</fieldset>
