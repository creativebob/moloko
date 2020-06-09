<fieldset>
    <legend>{{ $title ?? 'Значение' }}</legend>

    <div class="grid-x grid-margin-x">
    {{--    <div class="cell small-12">--}}
    {{--        <span>{{ $title ?? 'Значение' }}</span>--}}
    {{--    </div>--}}

        <div class="cell small-6">
            @php
                $nameMin = "{$name}_min";
                $nameMax = "{$name}_max";
            @endphp

            <label class="ininput">от:
                {!! Form::number("{$name}_min" ?? 'number_min', (request()->$nameMin ?? null), ['class' => 'input-text']) !!}
            </label>
        </div>

        <div class="cell small-6">
            <label class="ininput">до:
                {!! Form::number("{$name}_max" ?? 'number_max', (request()->$nameMax ?? null), ['class' => 'input-text']) !!}
            </label>
        </div>

    </div>
</fieldset>
