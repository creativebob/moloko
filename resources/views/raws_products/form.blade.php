<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">

        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-12 cell">
                <label>Название группы сырья
                    @include('includes.inputs.name', ['value' => $raws_product->name, 'required' => true])
                </label>
            </div>
            <div class="small-12 medium-12 cell">
                <label>Описание
                    @include('includes.inputs.varchar', ['name' => 'description', 'value' => $raws_product->description])
                </label>
            </div>

            <div class="small-12 medium-12 cell">
                <label>Категория
                    @include('includes.selects.raws_categories', ['raws_category_id' => isset($raws_product->raws_category_id) ? $raws_product->raws_category_id : null])
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

    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $raws_product])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>
</div>

