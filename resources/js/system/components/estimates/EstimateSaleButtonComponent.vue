<template>

        <input
                v-if="isShow"
                type="submit"
                value="Продать"
                class="button"
                @click="saleEstimate"
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
                return this.estimate.is_saled === 0 && this.$store.state.estimate.goodsItems.length > 0;
            },
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
