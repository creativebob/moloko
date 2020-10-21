<template>
    <a
        class="button"
        :disabled="isDisabled"
        v-if="isShow"
        @click="saleEstimate"
    >Продать</a>

</template>

<script>
    export default {
        data() {
            return {
                estimate: this.$store.state.lead.estimate
            }
        },
        computed: {
            isShow() {
                return this.estimate.registered_at && !this.estimate.saled_at;
            },
            isDisabled() {
                return this.$store.getters.PAYMENTS_AMOUNT >= this.$store.getters.ESTIMATE_AGGREGATIONS.total;
            }
        },
        methods: {
            saleEstimate() {
                this.$store.dispatch('SALE_ESTIMATE');
            },
        }
    }
</script>
