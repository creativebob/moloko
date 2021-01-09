<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-3">
        <label>Альтернативное название:
            {!! Form::text('name_alt', $priceService->name_alt) !!}
        </label>
    </div>

    <div class="cell small-12 medium-2">
        <label>Внешний ID:
            {!! Form::text('external', $priceService->external) !!}
        </label>
    </div>

</div>
