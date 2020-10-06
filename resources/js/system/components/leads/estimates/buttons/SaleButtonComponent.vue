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
                return this.estimate.is_registered === 1 && this.estimate.is_saled === 0;
            },
            isDisabled() {
                return this.$store.getters.paymentsAmount >= this.$store.getters.estimateTotal;
            }
        },
        methods: {
            saleEstimate() {
                this.$store.dispatch('SALE_ESTIMATE');
            },
        }
    }
</script>
