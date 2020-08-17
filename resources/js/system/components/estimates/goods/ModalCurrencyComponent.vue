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
                                :value="item.cost"
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
                                            :value="markupPercent"
                                            :disabled="true"
                                        ></digit-component>
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Наценка, руб
                                        <digit-component
                                            name="margin_currency"
                                            :value="markupCurrency"
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
                                            @change="changeDiscountPercent"
                                            :disabled="isRegistered"
                                            :limit-max="100"
                                            ref="discountPercentComponent"
                                        ></digit-component>
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Скидка, руб
                                        <digit-component
                                            name="discount_currency"
                                            :value="discountCurrency"
                                            @change="changeDiscountCurrency"
                                            :disabled="isRegistered"
                                            :limit-max="item.price"
                                            ref="discountCurrencyComponent"
                                        ></digit-component>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                    </div>



                    <div class="small-12 medium-6 cell">
                        <label>Количество, единиц
<!--                                <input-->
<!--                                    type="number"-->
<!--                                    name="count"-->
<!--                                    v-model="count"-->
<!--                                >-->
                            <digit-component
                                name="count"
                                :value="itemCount"
                                @change="changeCount"
                                :decimal-place="0"
                                :disabled="isRegistered"
                            ></digit-component>
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        Итоговая стоимость по позиции: {{ total | decimalPlaces | decimalLevel }} руб.
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
                    @click="updateItem"
                    class="button modal-button"
                >Сохранить</button>
            </div>
        </div>
        <div data-close class="icon-close-modal sprite close-modal add-item"></div>
    </div>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../inputs/DigitNestedComponent')
        },
        props: {
            item: Object,
            isRegistered: Boolean,
        },
        data() {
            return {
                markupPercent: 0,
                markupCurrency: 0,
                discountPercent: this.item.discount_percent,
                discountCurrency: this.item.discount_currency,

            }
        },
        mounted() {
            Foundation.reInit($('#modal-estimates_goods_item-' + this.item.id));

            this.markupCurrency = Number(this.item.price - this.item.cost);
            this.markupPercent = this.markupCurrency / (this.item.cost / 100);
        },
        computed: {
            estimate() {
                return this.$store.state.estimate.estimate;
            },
            total() {
                return (this.item.price - this.discountCurrency) * this.itemCount;
            },
            showComment() {
                if (this.item.comment != null) {
                    return this.item.comment.length > 0;
                } else {
                    return false;
                }
            },
            itemCount() {
                return Math.floor(this.item.count);
            }
        },
        methods: {
            changeCount(value) {
                this.item.count = value;
            },
            checkChangeCount() {
                if (this.item.product.serial === 0) {
                    if (!this.isRegistered) {
                        this.canChangeCount = !this.canChangeCount
                    }
                }
            },
            updateItem() {
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        count: this.itemCount,
                        discount_currency: this.discountCurrency,
                        discount_percent: this.discountPercent,
                    })
                    .then(response => {
                        $('#modal-estimates_goods_item-' + this.item.id).foundation('close');
                        this.$store.commit('UPDATE_GOODS_ITEM', response.data);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
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
            reset() {
                this.discountPercent = 0;
                this.$refs.discountPercentComponent.update(this.discountPercent);
                this.discountCurrency = 0;
                this.$refs.discountCurrencyComponent.update(this.discountCurrency);
            }
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
