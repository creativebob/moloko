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
    mounted() {
        if (this.$store.state.lead.paymentsMethods.length) {
            this.$emit('change', this.$store.state.lead.paymentsMethods[0].id);
        }
    },
    data() {
        return {
            paymentsMethodId: this.$store.state.lead.paymentsMethods.length > 0 ? this.$store.state.lead.paymentsMethods[0].id : null,
        }
    },
    computed: {
        paymentsMethods() {
            if (this.$store.state.lead.payments.length) {
                return this.$store.state.lead.paymentsMethods.filter(paymentsMethod => paymentsMethod.alias != 'full_prepayment')
            } else {
                return this.$store.state.lead.paymentsMethods;
            }
        }
    },
    methods: {
        change() {
            this.$emit('change', this.paymentsMethodId);
        }
    }
}
</script>
