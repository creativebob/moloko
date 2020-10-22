<template>
    <a
        class="button"
        v-if="isShow"
        @click="unregisterEstimate"
    >Отменить оформление</a>
</template>

<script>
export default {
    computed: {
        isShow() {
            return this.$store.state.lead.estimate.registered_at !== null && this.$store.getters.PAYMENTS_TOTAL == 0;
        },
    },
    methods: {
        unregisterEstimate() {
            this.$store.dispatch('UNREGISTER_ESTIMATE');
        }
    }
}
</script>
