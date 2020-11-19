<template>
    <div class="cell small-12">
        <div
            v-if="type == null"
            class="grid-x grid-padding-x selector-type-payment"
        >
            <div class="cell auto">
                <button
                    v-if="showCash"
                    type="button"
                    class="button hollow button-cash-type"
                    @click="setType('cash')"
                >Наличные
                </button>
                <button
                    v-if="showTerminal"
                    type="button"
                    class="button hollow button-electronically-type"
                    @click="setType('electronically')"
                >Банковская карта
                </button>
            </div>
            <div class="cell shrink">
                <button
                    v-if="showBank"
                    type="button"
                    class="button hollow button-bank-type"
                    @click="setType('bank')"
                >Оплата через банк
                </button>
            </div>
        </div>
        <template
            v-else
        >
            <div class="grid-x grid-padding-x align-right">
                <div class="cell small-2">
                    <button
                        @click="setType()"
                        type="button"
                        class="button tiny button-payment-back"
                    >Назад
                    </button>
                </div>
            </div>

            <div class="grid-x grid-padding-x">
                <div class="cell small-12 medium-4">
                    <label>Дата платежа:
                        <pickmeup-component
                            v-if="canChangeDate"
                            :name="type"
                            :required="true"
                            @change="changeDate"
                        ></pickmeup-component>
                        <input
                            v-else
                            type="text"
                            :value="paymentDate"
                            disabled
                        >
                    </label>
                </div>

                <div class="cell small-12 medium-8">
                    <label>Способ расчета:
                        <payments-methods-component></payments-methods-component>
                    </label>
                </div>
            </div>
        </template>

        <div class="grid-x grid-padding-x">
            <cash-component
                v-if="type == 'cash'"
                @change="changeCash"
                ref="cashComponent"
            ></cash-component>
            <electronically-component
                v-if="type == 'electronically'"
                @change="changeElectronically"
                ref="electronicallyComponent"
            ></electronically-component>
            <bank-component
                v-if="type == 'bank'"
                @change="changeElectronically"
                ref="bankComponent"
            ></bank-component>
        </div>

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 invert-show">
                <button
                    v-if="showButton"
                    @click="addPayment"
                    type="button"
                    class="button"
                >Принять оплату
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import moment from "moment";

export default {
    components: {
        'pickmeup-component': require('../../inputs/PickmeupComponent'),
        'payments-methods-component': require('./PaymentsMethodsComponent'),
        'cash-component': require('./types/CashComponent'),
        'electronically-component': require('./types/ElectronicallyComponent'),
        'bank-component': require('./types/BankComponent'),
    },
    data() {
        return {
            type: null,

            paymentDate: moment(String(new Date())).format('DD.MM.YYYY'),

            cash: 0,
            cashTaken: 0,
            cashChange: 0,

            electronically: 0,

            showButton: false
        }
    },
    computed: {
        showCash() {
            return this.$store.getters.HAS_OUTLET_SETTING('payment-cash');
        },
        showTerminal() {
            return this.$store.getters.HAS_OUTLET_SETTING('payment-terminal');
        },
        showBank() {
            return this.$store.getters.HAS_OUTLET_SETTING('bank-account');
        },

        canChangeDate() {
            let res = false;
            if (this.type) {
                if (this.type === 'bank') {
                    res = true
                } else {
                    const useCashRegister = this.$store.getters.HAS_OUTLET_SETTING('use-cash-register');
                    const paymentDateChange = this.$store.getters.HAS_OUTLET_SETTING('payment-date-change');

                    if (useCashRegister) {
                        res = false;
                    } else {
                        res = paymentDateChange;
                    }
                }
            }
            return res;
        },
        paymentsMethodId() {
            return this.$store.state.lead.paymentsMethodId;
        },

        autofill() {
            return this.$store.getters.HAS_OUTLET_SETTING('amount-autofill');
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
    },
    watch: {
        paymentsMethodId() {
            this.setShowButton();
        },
        electronically(val) {
            this.setShowButton();
        },
    },
    methods: {
        setType(type = null) {
            this.type = type;

            this.reset();
            this.setShowButton();
        },
        changeDate(date) {
            if (date !== "") {
                this.paymentDate = date;
            } else {
                this.paymentDate = null;
            }
        },
        changeCash(data) {
            this.cash = data.cash;

            this.electronically = data.electronically;

            this.setShowButton();
        },
        changeElectronically(val) {
            this.electronically = val;

            this.setShowButton();
        },
        setShowButton() {
            let res = false;

            if (this.type) {
                const alias = this.$store.getters.GET_PAYMENTS_METHOD_ALIAS(this.paymentsMethodId),
                    estimateTotal = this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total,
                    paymentsTotal = this.$store.getters.PAYMENTS_TOTAL;

                switch (alias) {
                    case 'full_payment':
                        res = this.cash + this.electronically + paymentsTotal >= estimateTotal;
                        break;

                    case 'full_prepayment':
                        res = this.cash + this.electronically >= estimateTotal;

                        break;

                    case 'partial_prepayment':
                        res = this.cash + this.electronically > 0;
                        break;
                }
            }

            this.showButton = res;
        },
        addPayment() {
            if (this.cash > 0 || this.electronically > 0) {
                const dateArray = this.paymentDate.split("."),
                    time = moment(String(new Date())).format('HH:mm:ss');
                const date = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0] + ' ' + time;

                const data = {
                    cash: this.cash,
                    cash_taken: this.cashTaken,
                    cash_change: this.cashChange,

                    electronically: this.electronically,

                    registered_at: date,
                    payments_method_id: this.paymentsMethodId,
                };
                this.$store.dispatch('ADD_PAYMENT', data);

                this.reset();

                switch (this.type) {
                    case 'cash':
                        this.$refs.cashComponent.reset();
                        break;

                    case 'electronically':
                        this.$refs.electronicallyComponent.reset();
                        break;

                    case 'bank':
                        this.$refs.bankComponent.reset();
                        break;
                }
            }
        },
        reset() {
            this.cash = 0;
            this.cashTaken = 0;
            this.cashChange = 0;

            this.electronically = 0;
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
