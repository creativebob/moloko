<template>
    <a
        class="button"
        v-if="isShow && userHasOutlet"
        @click="unregisterEstimate"
    >Отменить оформление</a>
</template>

<script>
export default {
    computed: {
        isShow() {
            return this.$store.state.lead.estimate.registered_at !== null && this.$store.getters.PAYMENTS_TOTAL == 0;
        },
        userHasOutlet() {
            return this.$store.getters.USER_HAS_OUTLET;
        }
    },
    methods: {
        unregisterEstimate() {
            this.$store.dispatch('UNREGISTER_ESTIMATE');
        }
    }
}
</script>
