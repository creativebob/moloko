<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-5">

        <div class="grid-x grid-padding-x">

            <div class="cell small-12 medium-6">
                <label>Триггер
                    @include('includes.inputs.name', ['name' => 'prom', 'value' => $promotion->prom])
                </label>
            </div>

            <div class="cell small-12 medium-3">
                <label>Минимальная сумма
                    <digit-component
                        name="total_min"
                        @if ($promotion->exists)
                        :value="{{ $promotion->total_min }}"
                        @endif
                    ></digit-component>
                </label>
            </div>

            <div class="cell small-12 medium-6">
                @include('system.common.listers.goods', ['items' => $promotion->goods->pluck('id')])
            </div>
        </div>

    </div>
</div>
