<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="grid-x grid-padding-x">

                <div class="small-12 cell">
                    <label>Название группы товара
                        @include('includes.inputs.name', ['value' => $goods_product->name, 'required' => true])
                    </label>
                </div>
                <div class="small-12 cell">
                    <label>Описание
                        @include('includes.inputs.varchar', ['name' => 'description', 'value' => $goods_product->description])
                    </label>
                </div>

                <div class="small-12 cell">
                    <label>Категория
                        @include('includes.selects.goods_categories', ['goods_category_id' => isset($goods_product->goods_category_id) ? $goods_product->goods_category_id : null])
                    </label>
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units_categories', ['default' => 6])
                </div>

                <div class="small-12 medium-6 cell">
                    @include('includes.selects.units', ['default' => 26, 'units_category_id' => 6])
                </div>


            </div>


        </div>
    </div>

    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    @if ($goods_product->articles->count() == 0)
    <div class="small-12 cell checkbox set-status">
        <input type="checkbox" name="set_status" id="set-status" value="set" {{ $goods_product->set_status == 'set' ? 'checked' : '' }}>
        {{-- {{ Form::checkbox('set_status', 'set', $goods_product->set_status, ['id' => 'set-status']) }} --}}
        <label for="set-status"><span>Набор</span></label>
    </div>
    @endif

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $goods_product])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>
</div>

