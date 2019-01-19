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

                    <div class="small-12 cell">
                        <label>Закупочная цена единицы, руб
                            @include('includes.inputs.digit', ['name'=>'cost', 'value'=>$workflow->cost ?? $workflow->product->cost, 'decimal_place'=>2])
                        </label>
                    </div>

                    <div class="small-12 medium-12 cell">
                        <fieldset>
                            <legend>Наценка:</legend>
                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Наценка, %
                                        @include('includes.inputs.digit', ['name'=>'margin_percent', 'value'=>$workflow->margin_percent, 'decimal_place'=>4])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Наценка, руб
                                        @include('includes.inputs.digit', ['name'=>'margin_currency', 'value'=>$workflow->margin_currency, 'decimal_place'=>2])
                                    </label>
                                </div>

                                <hr>

                                <div class="small-12 medium-6 cell">
                                    <label>Допфикс наценка, %
                                        @include('includes.inputs.digit', ['name'=>'extra_margin_percent', 'value'=>$workflow->extra_margin_percent, 'decimal_place'=>4])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Допфикс наценка, руб
                                        @include('includes.inputs.digit', ['name'=>'extra_margin_currency', 'value'=>$workflow->extra_margin_currency, 'decimal_place'=>2])
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>


                    <div class="small-12 medium-12 cell">
                        <fieldset>
                            <legend>Скидка:</legend>
                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, %
                                        @include('includes.inputs.digit', ['name'=>'discount_percent', 'value'=>$workflow->discount_percent, 'decimal_place'=>4])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, руб
                                        @include('includes.inputs.digit', ['name'=>'discount_currency', 'value'=>$workflow->discount_currency, 'decimal_place'=>2])
                                    </label>
                                </div>

                                <div class="small-12 medium-6 cell">
                                    <label>Допфикс скидка, %
                                        @include('includes.inputs.digit', ['name'=>'extra_discount_percent', 'value'=>$workflow->extra_discount_percent, 'decimal_place'=>4])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Допфикс скидка, руб
                                        @include('includes.inputs.digit', ['name'=>'extra_discount_currency', 'value'=>$workflow->extra_discount_currency, 'decimal_place'=>2])
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                   

                    <div class="small-12 medium-6 cell">
                        <label>Цена единицы, руб
                            @include('includes.inputs.digit', ['name'=>'sum', 'value'=>$workflow->sum, 'decimal_place'=>2])
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Количество, единиц
                            @include('includes.inputs.digit', ['name'=>'count', 'value'=>$workflow->count])
                        </label>
                    </div>

                    <div class="small-12 cell">
                        <label>Итоговая стоимость по позиции, руб
                            @include('includes.inputs.digit', ['name'=>'total', 'value'=>$workflow->total ?? $workflow->product->price, 'decimal_place'=>2])
                        </label>
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
            {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id' => 'submit-pricing']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')


<script>



'use strict';

class PricingCalc {

    constructor(name) {
        this.name = name;

        // Проверяем адекватность указанных в поле данных:
        // var reg=/^\d+$/
        // if (reg.test(limit_value) || limit_value == null){
        //     this.limit = limit_value;
        // } else {
        //     alert('Ограничивающее значение для цифрового поля ID: digitfield-' + this.name + ' задано не верно!');
        // };

        // this.cost = cost;

        // this.margin_percent = margin_percent;
        // this.margin_currency = margin_currency;
        // this.margin_priority_currency = margin_priority_currency;

        // this.extra_margin_percent = extra_margin_percent;
        // this.extra_margin_currency = extra_margin_currency;
        // this.extra_margin_priority_currency = extra_margin_priority_currency;

        // this.discount_percent = discount_percent;
        // this.discount_currency = discount_currency;
        // this.discount_priority_currency = discount_priority_currency;

        // this.extra_discount_percent = extra_discount_percent;
        // this.extra_discount_currency = extra_discount_currency;
        // this.extra_discount_priority_currency = extra_discount_priority_currency;

        // this.count = count;
        // this.sum = sum;
        // this.total= total;
    }

}

// Создаем калькулятор ценообразования
let PricingCalcOrder = new PricingCalc(
    'workflow_calc');


$("#digitfield-count").keyup(function(event) {

    var count = $("#digitfield-count").val();
    var sum = $("#digitfield-sum").val();

    var total = sum*count;
    $("#digitfield-total").val(total.toFixed(2));
});

$("#digitfield-cost").keyup(function(event) {

    var cost = $("#digitfield-cost").val();
    var margin_currency = $("#digitfield-margin_currency").val();
    var margin_percent = margin_currency*100/cost;

    var count = $("#digitfield-count").val();
    var sum = cost*1 + margin_currency*1;
    $("#digitfield-sum").val(sum.toFixed(2));

    $("#digitfield-margin_percent").val(margin_percent.toFixed(2));

    var total = sum*count;
    $("#digitfield-total").val(total.toFixed(2));
});

$("#digitfield-margin_percent").keyup(function(event) {

    var cost = $("#digitfield-cost").val();

    var count = $("#digitfield-count").val();
    var margin_percent = $("#digitfield-margin_percent").val();
    var margin_currency = margin_percent*cost/100;

    $("#digitfield-margin_currency").val(margin_currency);

    var sum = cost*1 + margin_currency*1;
    $("#digitfield-sum").val(sum.toFixed(2));

    var total = sum*count;
    $("#digitfield-total").val(total.toFixed(2));

});

$("#digitfield-margin_currency").keyup(function(event) {

    var cost = $("#digitfield-cost").val();
    var margin_currency = $("#digitfield-margin_currency").val();
    var margin_percent = margin_currency*100/cost;

    $("#digitfield-margin_percent").val(margin_percent.toFixed(2));

    var sum = cost*1 + margin_currency*1;
    $("#digitfield-sum").val(sum.toFixed(2));

    var total = sum*count;
    $("#digitfield-total").val(total.toFixed(2));

});

</script>