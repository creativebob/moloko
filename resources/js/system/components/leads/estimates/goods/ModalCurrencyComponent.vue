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
                            <legend>Скидка:</legend>
                            <div class="grid-x grid-margin-x">
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, %
                                        <digit-component
                                            name="discount_percent"
                                            :value="discountPercent"
                                            @input="changeDiscountPercent"
                                            :disabled="isRegistered"
                                            :limit="100"
                                            ref="discountPercentComponent"
                                        ></digit-component>
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, руб
                                        <digit-component
                                            name="discount_currency"
                                            :value="discountCurrency"
                                            @input="changeDiscountCurrency"
                                            :disabled="isRegistered"
                                            :limit="item.price"
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
            item: Object,
        },
        data() {
            return {
                discountPercent: this.item.discount_percent,
                discountCurrency: this.item.discount_currency / this.item.count,
                curCount: this.item.count,
            }
        },
        mounted() {
            Foundation.reInit($('#modal-estimates_goods_item-' + this.item.id));
        },
        computed: {
            isRegistered() {
                return this.$store.state.lead.estimate.is_registered == 1;
            },
            count: {
                get() {
                    return this.curCount;
                },
                set (value) {
                    this.curCount = value;
                }
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

            // item(obj) {
            //     alert(1)
            //     this.reset();
            // }
            // discountPercent(val) {
            //     this.discountPercent = val;
            // },
            // discountCurrency(val) {
            //     this.discountCurrency = val / this.curCount;
            // },
        },
        methods: {
            changeDiscountPercent(value) {
                let percent = this.item.price / 100;
                this.discountPercent = value;
                this.discountCurrency = value * percent;
                this.$refs.discountCurrencyComponent.update(this.discountCurrency);
            },
            changeDiscountCurrency(value) {
                let percent = this.item.price / 100;
                this.discountCurrency = value;
                this.discountPercent = value / percent;
                this.$refs.discountPercentComponent.update(this.discountPercent);
            },
            changeCount(value) {
                this.curCount = value;
            },
            update() {
                if (this.item.discount_percent != this.discountPercent || (this.item.discount_currency / this.item.count) != this.discountCurrency) {
                    this.item.manual_discount_currency = this.discountCurrency;
                    this.item.manual_discount_percent = this.discountPercent;
                }

                this.item.count = this.curCount;

                $('#modal-estimates_goods_item-' + this.item.id).foundation('close');

                this.$emit('update', this.item);
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
