<template>
    <div class="cell small-12">
        <div
            v-if="showDenominations"
            class="grid-x grid-padding-x"
        >
            <div class="cell shrink">
                <button
                    @click="setTotal"
                    type="button"
                    class="button hollow"
                >Без сдачи
                </button>
            </div>
            <div class="cell auto self-right">
                <div class="grid-x grid-padding-x">
                    <div class="cell auto self-left denominations">
                        <button
                            v-for="denomination in denominations"
                            type="button"
                            :class="'button hollow tiny denomination-' + denomination"
                            @click="addDenomination(denomination)"
                        >{{ denomination }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-auto fields-payment-wrap">
                <div class="grid-x grid-padding-x">
                    <div class="cell small-12 medium-shrink payment-cash-wrap">
                        <label>Наличными:
                            <digit-component
                                classes="input-payment cash"
                                v-model="cash"
                                ref="cashComponent"
                                :limit-max="1000000"
                                @input="denomination = false"
                                v-focus
                            ></digit-component>
                        </label>
                        <button
                            v-if="!mixed && showMixed && showTerminal"
                            type="button"
                            class="button-add-electronically"
                            @click="openElectronically"
                        >+ доплата по карте
                        </button>
                    </div>
                    <div class="cell small-12 medium-shrink payment-electronically-wrap">
                        <div
                            v-if="mixed"
                            class="grid-x electronically"
                        >
                            <div class="cell auto">
                                <label>По карте:
                                    <digit-component
                                        classes="input-payment electronically"
                                        :value="electronically"
                                        :limit-max="debitTotal"
                                        v-model="electronically"
                                        ref="electronicallyComponent"
                                    ></digit-component>
                                </label>
                            </div>
                            <div class="cell shrink">
                                <div
                                    @click="closeElectronically"
                                    class="icon-delete sprite"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div
                        v-if="change > 0.01"
                        class="cell small-12 medium-shrink change-wrap"
                    >
                        <span class="change-text">Сдача клиенту: </span><span
                        class="change-value">{{ change | decimalPlaces | decimalLevel }}<span> руб.</span></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    components: {
        'digit-component': require('../../../inputs/DigitComponent'),
    },
    data() {
        return {
            denominations: [50, 100, 200, 500, 1000, 2000, 5000],

            cash: 0,
            cashTaken: 0,
            cashChange: 0,

            electronically: 0,

            mixed: false,
            isDenomination: false
        }
    },
    mounted() {
        this.checkAutofill();
    },
    computed: {
        paymentsMethodId() {
            return this.$store.state.lead.paymentsMethodId;
        },
        showTerminal() {
            return this.$store.getters.HAS_OUTLET_SETTING('payment-terminal');
        },
        showDenominations() {
            return this.$store.getters.HAS_OUTLET_SETTING('denominations-show');
        },
        estimateTotal() {
            return this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
        },
        paymentsTotal() {
            return this.$store.getters.PAYMENTS_TOTAL;
        },
        debitTotal() {
            return this.estimateTotal - this.paymentsTotal;
        },
        total() {
            return this.cash + this.electronically;
        },
        change() {
            return this.total - this.debitTotal;
        },
        showMixed() {
            return this.cash < this.debitTotal;
        },
        data() {
            return {
                cash: this.cash,
                cash_taken: this.cashTaken,
                cash_change: this.cashChange,

                electronically: this.electronically,
            };
        }
    },
    watch: {
        cash() {
            this.$emit('change', this.data);
        },
        electronically() {
            this.$emit('change', this.data);
        },
        paymentsMethodId() {
            this.resetCash();
            this.resetElectronically();
            this.checkAutofill();
        }
    },
    methods: {
        setTotal() {
            this.resetCash();
            this.resetElectronically();

            this.isDenomination = false;

            this.$store.commit('SET_PAYMENTS_METHOD_ID');

            this.cash = this.estimateTotal - this.paymentsTotal;
            this.$refs.cashComponent.update(this.cash);
            this.mixed = false;
        },
        addDenomination(denomination) {
            if (!this.isDenomination) {
                this.resetCash();
                this.resetElectronically();

                this.isDenomination = true;
            }
            this.cash = parseFloat(this.cash) + denomination;
            this.$refs.cashComponent.update(this.cash);
        },
        changePaymentsMethodId(id) {
            this.paymentsMethodId = id;
            this.setShowButton();
        },
        setCash(value) {
            this.cash = value;
            this.setShowButton();
        },
        setElectronically(value) {
            this.electronically = value;
            this.setShowButton();
        },
        setShowButton() {
            const alias = this.$store.getters.GET_PAYMENTS_METHOD_ALIAS(this.paymentsMethodId);
            let res;

            switch (alias) {
                case 'full_payment':
                    res = this.total >= this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
                    break;

                case 'full_prepayment':
                    res = this.total >= this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total;
                    break;

                case 'partial_prepayment':
                    res = this.total > 0;
                    break;

                default:
                    res = false
                    break;
            }
            this.showButton = res;
        },
        openElectronically() {
            this.electronically = 0;
            this.mixed = true;
        },
        closeElectronically() {
            this.electronically = 0;
            this.mixed = false;
        },
        reset() {
            this.resetCash();
            this.resetElectronically();
        },
        resetCash() {
            this.cash = 0;
            this.$refs.cashComponent.update(this.cash);
            this.cashTaken = 0;
            this.cashChange = 0;

            this.isDenomination = false;
        },
        resetElectronically() {
            if (this.electronically > 0) {
                this.electronically = 0;
                this.$refs.electronicallyComponent.update(this.electronically);
            }

            this.mixed = false;
        },
        checkAutofill() {
            if (this.$store.getters.HAS_OUTLET_SETTING('amount-autofill')) {
                const alias = this.$store.getters.GET_PAYMENTS_METHOD_ALIAS(this.paymentsMethodId);
                if (alias === 'full_payment' || alias === 'full_prepayment') {
                    this.cash = this.debitTotal;
                    this.$refs.cashComponent.update(this.cash);
                } else {
                    this.cash = 0;
                    this.$refs.cashComponent.update(this.cash);
                }
            } else {
                this.cash = 0;
                this.$refs.cashComponent.update(this.cash);
            }
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
