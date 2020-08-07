<template>
    <input
        v-if="isShow"
        type="submit"
        value="Произвести"
        class="button"
        @click="produceEstimate"
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
                return (this.estimate.is_produced === 0) && (this.$store.state.estimate.goodsItems.length > 0 || this.$store.state.estimate.servicesItems.length > 0);
            },
        },
        methods: {

            produceEstimate() {
                if (this.isShow) {
                    $('form').attr('action', '/admin/estimates/' + this.estimate.id + '/produce');
                }
            },
        }
    }
</script>
