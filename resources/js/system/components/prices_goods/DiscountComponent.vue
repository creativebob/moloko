<template>
    <div class="grid-x grid-padding-x">
        <div class="cell small-12 medium-3">
            <label>Цена
                <digit-component
                    name="price"
                    :value="price"
                    @change="changePrice"
                    ref="priceComponent"
                    :required="true"
                ></digit-component>
            </label>
        </div>
        <div class="cell small-12 medium-3">
            <label>Скидка, %
                <digit-component
                    name="discount_percent"
                    :value="discountPercent"
                    :limit="100"
                    @change="changeDiscountPercent"
                    ref="discountPercentComponent"
                ></digit-component>
            </label>
        </div>
        <div class="cell small-12 medium-3">
            <label>Скидка, руб
                <digit-component
                    name="discount_currency"
                    :value="discountCurrency"
                    :limit="price"
                    @change="changeDiscountCurrency"
                    ref="discountCurrencyComponent"
                ></digit-component>
            </label>
        </div>
        <div class="cell small-12 medium-3">
            Итого<br>{{ total | decimalPlaces | decimalLevel }} {{ item.currency.abbreviation}}
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../inputs/DigitComponent')
        },
        props: {
            item: Object,
        },
        data() {
            return {
                price: this.item.price,
                discountPercent: this.item.discount_percent,
                discountCurrency: this.item.discount_currency,
            }
        },
        computed: {
            total() {
                return this.price - this.discountCurrency;
            }
        },
        methods: {
            changePrice(value) {
                this.price = value;
                this.discountCurrency = value / 100 * this.discountPercent;
                this.$refs.discountCurrencyComponent.update(this.discountCurrency);
                this.$refs.discountPercentComponent.update(this.discountPercent);
            },
            changeDiscountPercent(value) {
                let percent = this.price / 100;
                this.discountPercent = value;
                this.discountCurrency = value * percent;
                this.$refs.discountCurrencyComponent.update(this.discountCurrency);
            },
            changeDiscountCurrency(value) {
                let percent = this.price / 100;
                this.discountCurrency = value;
                this.discountPercent = value / percent;
                this.$refs.discountPercentComponent.update(this.discountPercent);
            },
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        }
    }
</script>
