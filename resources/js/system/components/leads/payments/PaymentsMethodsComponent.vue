<template>
    <select
        v-if="paymentsMethods.length"
        v-model="paymentsMethodId"
        @change="change"
    >
        <option
            v-for="paymentsMethod in paymentsMethods"
            :value="paymentsMethod.id"
        >{{ paymentsMethod.name }}</option>
    </select>

</template>

<script>
export default {
    data() {
        return {
            paymentsMethodId: this.$store.state.lead.paymentsMethodId,
        }
    },
    computed: {
        paymentsMethods() {
            if (this.$store.getters.ACTUAL_PAYMENTS.length) {
                return this.$store.state.lead.paymentsMethods.filter(paymentsMethod => paymentsMethod.alias != 'full_prepayment')
            } else {
                return this.$store.state.lead.paymentsMethods;
            }
        },
        storePaymentsMethodId() {
            return this.$store.state.lead.paymentsMethodId;
        }
    },
    watch: {
        storePaymentsMethodId(val) {
            this.paymentsMethodId = val;
        }
    },
    methods: {
        change() {
            this.$store.commit('SET_PAYMENTS_METHOD_ID', this.paymentsMethodId)
        }
    }
}
</script>
