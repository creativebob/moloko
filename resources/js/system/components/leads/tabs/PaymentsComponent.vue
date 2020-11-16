<template>
    <li
        v-show="isRegistered && hasPaymentsMethods"
        class="tabs-title">
        <a data-tabs-target="tab-payments" href="#tab-payments">Оплата</a>
    </li>
</template>

<script>
    export default {
        computed: {
            isRegistered() {
                return this.$store.state.lead.estimate.registered_at !== null;
            },
            hasPaymentsMethods() {
                return this.$store.state.lead.paymentsMethods.length > 0
            }
        }
    }
</script>
