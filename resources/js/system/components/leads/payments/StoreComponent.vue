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
                            :name="type"
                            :required="true"
                            @change="changeDate"
                        ></pickmeup-component>
                    </label>
                </div>

                <div class="cell small-12 medium-8">
                    <label>Способ расчета:
                        <payments-methods-component
                            @change="changePaymentsMethodId"
                        ></payments-methods-component>
                    </label>
                </div>
            </div>
        </template>

        <div class="grid-x grid-padding-x">
            <cash-component
                v-if="type == 'cash'"
                @change="changeFromCash"
                ref="cashComponent"
            ></cash-component>
            <div
                v-else-if="type == 'electronically' || type == 'bank'"
                class="cell shrink"
            >
                <label>Сумма:
                    <digit-component
                        classes="input-payment electronically"
                        v-model="electronically"
                        ref="electronicallyComponent"
                        v-focus
                    ></digit-component>
                </label>
            </div>
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
            paymentsMethodId: null,

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
    },
    watch: {
        electronically(val) {
            this.setShowButton();
        }
    },
    methods: {
        setType(type = null) {
            this.type = type;
            this.reset();
        },
        changeDate(date) {
            if (date !== "") {
                this.paymentDate = date;
            } else {
                this.paymentDate = null;
            }
        },
        changePaymentsMethodId(id) {
            this.paymentsMethodId = id;
            this.setShowButton();
        },
        changeFromCash(data) {
            this.cash = data.cash;

            this.electronically = data.electronically;

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
                const dateArray = this.paymentDate.split(".");
                const date = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0] + ' 00:00:00';

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

                if (this.type === 'cash') {
                    this.$refs.cashComponent.reset();
                } else {
                    this.$refs.electronicallyComponent.update(this.electronically);
                }
            }
        },
        reset() {
            this.cash = 0;
            this.cashTaken = 0;
            this.cashChange = 0;

            this.electronically = 0;
        }
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
