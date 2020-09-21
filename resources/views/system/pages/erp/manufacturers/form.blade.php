<div class="grid-x">

    <div class="cell small-12 medium-6 large-5">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <label>Комментарий к производителю
                    @include('includes.inputs.textarea', ['name'=>'manufacturer_description', 'value' => $manufacturer->description])
                </label>
            </div>

            <div class="cell small-12 checkbox">
                {!! Form::hidden('is_partner', 0) !!}
                {!! Form::checkbox('is_partner', 1, $manufacturer->is_partner, ['id' => 'checkbox-is_partner']) !!}
                <label for="checkbox-is_partner"><span>Партнер</span></label>
            </div>
        </div>
    </div>

</div>
