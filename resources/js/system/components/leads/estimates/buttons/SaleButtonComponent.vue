<template>

        <input
                v-if="isShow"
                type="submit"
                value="Продать"
                class="button"
                @click="saleEstimate"
                :disabled="!isDisabled"
        >

</template>

<script>
    export default {
        data() {
            return {
                estimate: this.$store.state.estimate.estimate
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
                if (this.isShow) {
                        $('form').attr('action', '/admin/estimates/' + this.estimate.id + '/saling');
                }
            },
        }
    }
</script>
