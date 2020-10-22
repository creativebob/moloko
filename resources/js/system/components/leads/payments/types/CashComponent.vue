<template>
    <div class="cell small-12">
        <div class="grid-x grid-padding-x">
            <div class="cell shrink">
                <button
                    @click="setTotal"
                    type="button"
                    class="button hollow"
                >Без сдачи</button>
            </div>
            <div class="cell auto self-right">
                <div class="grid-x grid-padding-x">
                    <div class="cell auto self-left denominations">
                        <button
                            v-for="denomination in denominations"
                            type="button"
                            :class="'button hollow tiny denomination-' + denomination"
                            @click="addDenomination(denomination)"
                        >{{ denomination }}</button>
                    </div>
                    <div class="cell shrink self-right">
                        <button
                            @click="resetType"
                            type="button"
                            class="button tiny button-payment-back"
                        >Назад
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-auto fields-payment-wrap">
                <div class="grid-x grid-padding-x">
                    <div class="cell small-12 medium-6 payment-cash-wrap">
                        <label>Наличными:
                            <digit-component
                                classes="input-payment cash"
                                @input="setCash"
                                ref="cashComponent"
                                v-focus
                            ></digit-component>
                        </label>
                        <button
                            v-if="isElectronically && !mixed && showMixed"
                            type="button"
                            class="button-add-electronically"
                            @click="setMixed"
                        >+ доплата по карте</button>
                    </div>
                    <div class="cell small-12 medium-6 payment-electronically-wrap">
                        <div
                            v-if="mixed"
                            class="electronically"
                        >
                            <label>По карте:
                                <digit-component
                                    classes="input-payment electronically"
                                    :value="electronically"
                                    :limit-max="estimateTotal"
                                    @input="setElectronically"
                                    ref="electronicallyComponent"
                                ></digit-component>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="change > 0"
                class="cell small-12 medium-shrink change-wrap"
            >
                <span class="change-text">Сдача клиенту: </span><span class="change-value">{{ change | decimalPlaces | decimalLevel }}<span> руб.</span></span>
            </div>
        </div>

        <div class="grid-x grid-padding-x">
            <div
                v-if="showButton"
                class="cell small-12 invert-show"
            >
                <button
                    @click="addPayment"
                    type="button"
                    class="button"
                >Принять оплату и закрыть чек</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../../inputs/DigitComponent'),
        },
        props: {
            currencies: Array,
            isElectronically: Boolean
        },
        data() {
            return {
                denominations: [50, 100, 200, 500, 1000, 2000, 5000],
                cash: 0,
                electronically: 0,
                mixed: false
            }
        },
        computed: {
            estimateTotal() {
                return this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
            },
            change() {
                return this.cash + this.electronically - this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
            },
            showMixed() {
                return this.cash < this.estimateTotal;
            },
            showButton() {
                if (this.cash > 0 && this.electronically == 0) {
                    return this.cash >= this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
                }

                if (this.cash == 0 && this.electronically > 0) {
                    return this.electronically >= this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
                }

                if (this.cash > 0 && this.electronically > 0) {
                    return (this.cash + this.electronically) >= this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
                }
            }
        },
        methods: {
            setTotal() {
                this.cash = this.estimateTotal;
                this.$refs.cashComponent.update(this.cash);
            },
            addDenomination(denomination) {
                this.cash = parseFloat(this.cash) + denomination;
                this.$refs.cashComponent.update(this.cash);
            },
            setCash(value) {
                this.cash = value;
            },
            setElectronically(value) {
                this.electronically = value;
            },
            resetType() {
                this.$emit('reset');
            },
            setMixed() {
                const value = this.estimateTotal - this.cash;
                this.electronically = (value > 0) ? value : 0;
                this.mixed = true;
            },
            addPayment() {
                const data = {
                    cash: this.estimateTotal - this.electronically,
                    change: this.change,
                    electronically: this.electronically,
                };
                this.$store.dispatch('ADD_PAYMENT', data);
                this.cash = 0;
            }
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
        },
    }
</script>
