<template>
    <div
        class="reveal"
        :id="'modal-estimates_goods_item-' + item.id"
        data-reveal
        data-close-on-click="false"
        v-reveal
    >
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>Настройка позиции</h5>
            </div>
        </div>

        <div class="grid-x grid-padding-x align-center modal-content inputs">
            <div class="small-12 cell">

                <div class="grid-x grid-margin-x">

                    <div class="small-12 medium-6 cell">
                        <label>Закупочная цена единицы, руб
                            <digit-component
                                name="cost"
                                :value="item.cost_unit"
                                :disabled="true"
                            ></digit-component>
                        </label>
                    </div>
                    <div class="small-12 medium-6 cell">
                        <label>Цена единицы, руб
                            <digit-component
                                name="price"
                                :value="item.price"
                                :disabled="true"
                            ></digit-component>
                        </label>
                    </div>

                    <div class="small-12 medium-12 cell">
                        <fieldset>
                            <legend>Наценка:</legend>
                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Наценка, %
                                        <digit-component
                                            name="margin_percent"
                                            :value="item.margin_percent_unit"
                                            :disabled="true"
                                        ></digit-component>
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Наценка, руб
                                        <digit-component
                                            name="margin_currency"
                                            :value="item.margin_currency_unit"
                                            :disabled="true"
                                        ></digit-component>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="small-12 medium-12 cell">
                        <fieldset>
                            <legend>Скидка <span
                                v-if="manual"
                                @click="returnComputed"
                            >(удалить ручную)</span>:</legend>
                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, %
                                        <digit-component
                                            :value="discountPercent"
                                            @input="changeDiscountPercent"
                                            :disabled="isRegistered"
                                            :limit-max="100"
                                            ref="discountPercentComponent"
                                        ></digit-component>
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, руб
                                        <digit-component
                                            :value="discountCurrency"
                                            @input="changeDiscountCurrency"
                                            :disabled="isRegistered"
                                            :limit-max="item.price"
                                            ref="discountCurrencyComponent"
                                        ></digit-component>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>


                    <div class="cell small-12">
                        <label>Количество
                            <span
                                v-if="isRegistered || item.goods.serial === 1"
                            >{{ item.count | decimalPlaces | decimalLevel }}</span>
                            <count-component
                                v-else
                                :count="item.count"
                                :limit-min="1"
                                @update="changeCount"
                                ref="countComponent"
                            ></count-component>
                        </label>
                    </div>

                    <div class="cell small-12">
                        <table class="modal-item-estimate-total">
                            <tbody>
                                <tr>
                                    <td>Общая скидка</td>
                                    <td class="width-limit-3">{{ totalDiscount | decimalPlaces | decimalLevel }}</td>
                                </tr>
                                <tr>
                                    <td>Общая маржа</td>
                                    <td class="width-limit-3">{{ totalMargin | decimalPlaces | decimalLevel }}</td>
                                </tr>
                                <tr>
                                    <td>ИТОГО по позиции:</td>
                                    <td class="width-limit-3"><span class="invert-show">{{ total | decimalPlaces | decimalLevel }}</span> руб.</td>

                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>

                <!--                    <div class="grid-x grid-margin-x">-->
                <!--                        <div class="small-12 cell">-->
                <!--                            Склад:-->
                <!--                        </div>-->
                <!--                    </div>-->

            </div>
        </div>
        <div
            v-if="! isRegistered"
            class="grid-x align-center"
        >
            <div class="small-6 medium-4 cell">
                <button
                    @click="update"
                    class="button modal-button"
                >Сохранить
                </button>
            </div>
        </div>
        <div
            @click="reset"
            data-close
            class="icon-close-modal sprite close-modal add-item"
        ></div>
    </div>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../../inputs/DigitComponent'),
            'count-component': require('../../../inputs/CountWithButtonsComponent'),
        },
        props: {
            id: Number,
        },
        data() {
            return {
                isManual: this.$store.getters.GOODS_ITEM(this.id).is_manual == 1,
                discountPercent: this.$store.getters.GOODS_ITEM(this.id).discount_percent,
                discountCurrency: this.$store.getters.GOODS_ITEM(this.id).discount_currency / parseFloat(this.$store.getters.GOODS_ITEM(this.id).count),
                curCount: parseFloat(this.$store.getters.GOODS_ITEM(this.id).count),
            }
        },
        mounted() {
            Foundation.reInit($('#modal-estimates_goods_item-' + this.id));

            // this.isManual = this.$store.getters.GOODS_ITEM(this.id).is_manual == 1;
            // this.discountPercent = this.$store.getters.GOODS_ITEM(this.id).discount_percent;
            // this.discountCurrency = this.$store.getters.GOODS_ITEM(this.id).discount_currency;
            // this.curCount = this.$store.getters.GOODS_ITEM(this.id).count;
        },
        computed: {
            item() {
                return this.$store.getters.GOODS_ITEM(this.id);
            },
            isRegistered() {
                return this.$store.state.lead.estimate.registered_at !== null;
            },
            count: {
                get() {
                    return parseFloat(this.$store.getters.GOODS_ITEM(this.id).count);
                },
                set (value) {
                    this.curCount = value;
                }
            },
            manual() {
                return this.isManual;
            },
            totalDiscount() {
                return this.discountCurrency * this.curCount;
            },
            totalMargin() {
                return (this.item.price - this.discountCurrency - this.item.cost_unit) * this.curCount;
            },
            total() {
                return (this.item.price - this.discountCurrency) * this.curCount;
            },
        },
        watch: {
            count(val) {
                this.curCount = val;
            },
        },
        methods: {
            changeDiscountPercent(value) {
                let percent = this.item.price / 100;
                this.discountPercent = value;
                this.discountCurrency = value * percent;
                this.isManual = true;
                this.$refs.discountCurrencyComponent.update(this.discountCurrency);
            },
            changeDiscountCurrency(value) {
                let percent = this.item.price / 100;
                this.discountCurrency = value;
                this.discountPercent = value / percent;
                this.isManual = true;
                this.$refs.discountPercentComponent.update(this.discountPercent);
            },
            changeCount(value) {
                this.curCount = value;
            },
            returnComputed() {
                this.isManual = false;

                this.discountPercent = this.item.computed_discount_percent;
                this.$refs.discountPercentComponent.update(this.discountPercent);

                this.discountCurrency = this.item.computed_discount_currency;
                this.$refs.discountCurrencyComponent.update(this.discountCurrency);
            },
            update() {
                let data = {
                    id: this.id
                };
                if (this.isManual) {
                    data.manual_discount_currency = this.discountCurrency;
                    data.manual_discount_percent = this.discountPercent;
                    data.is_manual = 1;
                } else {
                    data.manual_discount_currency = 0;
                    data.manual_discount_percent = 0;
                    data.is_manual = 0;
                }

                data.count = this.curCount;

                this.$emit('update', data);
                $('#modal-estimates_goods_item-' + this.id).foundation('close');
            },
            reset() {
                if (!this.isRegistered) {
                    this.discountPercent = this.item.discount_percent;
                    this.$refs.discountPercentComponent.update(this.discountPercent);

                    this.discountCurrency = this.item.discount_currency / this.item.count;
                    this.$refs.discountCurrencyComponent.update(this.discountCurrency);

                    this.curCount = this.item.count;
                    this.$refs.countComponent.update(this.curCount);
                }
            },
        },
        directives: {
            'reveal': {
                bind: function (el) {
                    new Foundation.Reveal($(el))
                },
            }
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },
            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return parseInt(value).toLocaleString();
            },

            // Отбраcывает дробную часть в строке с числами
            onlyInteger(value) {
                return Math.floor(value);
            },
        },
    }
</script>
