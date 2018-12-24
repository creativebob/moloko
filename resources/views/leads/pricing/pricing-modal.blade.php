<div class="reveal" id="pricing" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ЦЕНООБРАЗОВАНИЕ</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-pricing', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">
                <div class="small-12 medium-6 cell">
                    <div class="small-12 cell">
                        <label>Закупочная цена
                            @include('includes.inputs.digit', ['name'=>'cost', 'value'=>$order_composition->cost])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Наценка, %
                            @include('includes.inputs.digit', ['name'=>'margin_percent', 'value'=>$order_composition->margin_percent])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Наценка, руб
                            @include('includes.inputs.digit', ['name'=>'margin_percent', 'value'=>$order_composition->margin_percent])
                        </label>
                    </div>
                </div>
                <div class="small-12 medium-6 cell">

                </div>
            </div>

            <div class="grid-x grid-margin-x">
                {{ Form::hidden('id', null) }}
                {{ Form::hidden('model', null) }}
            </div>
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить задачу', ['class'=>'button modal-button', 'id' => 'submit-pricing']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')