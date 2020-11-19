<template>
    <div class="cell shrink">
        <label>Сумма:
            <digit-component
                classes="input-payment electronically"
                v-model="electronically"
                ref="electronicallyComponent"
                :limit-max="debitTotal"
                v-focus
            ></digit-component>
        </label>
    </div>
</template>

<script>
export default {
    components: {
        'digit-component': require('../../../inputs/DigitComponent'),
    },
    data() {
        return {
            electronically: 0,
        }
    },
    mounted() {
        this.checkAutofill();
    },
    computed: {
        paymentsMethodId() {
            return this.$store.state.lead.paymentsMethodId;
        },
        debitTotal() {
            return this.$store.getters.ESTIMATE_AGGREGATIONS.estimate.total - this.$store.getters.PAYMENTS_TOTAL;
        },
    },
    watch: {
        electronically(val) {
            this.$emit('change', val);
        },
        paymentsMethodId() {
            this.checkAutofill();
        }
    },
    methods: {
        reset() {
            this.electronically = 0;
            this.$refs.electronicallyComponent.update(this.electronically);
        },
        checkAutofill() {
            if (this.$store.getters.HAS_OUTLET_SETTING('amount-autofill')) {
                const alias = this.$store.getters.GET_PAYMENTS_METHOD_ALIAS(this.paymentsMethodId);
                if (alias === 'full_payment' || alias === 'full_prepayment') {
                    this.electronically = this.debitTotal;
                    this.$refs.electronicallyComponent.update(this.electronically);
                } else {
                    this.electronically = 0;
                    this.$refs.electronicallyComponent.update(this.electronically);
                }
            } else {
                this.electronically = 0;
                this.$refs.electronicallyComponent.update(this.electronically);
            }
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
