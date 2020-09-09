<template>
    <input
        v-if="isShow"
        type="submit"
        value="Оформить"
        class="button"
        @click="registerEstimate"
        :disabled="isDisabled"
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
                return this.estimate.is_registered === 0 && (this.$store.state.estimate.goodsItems.length > 0 || this.$store.state.estimate.servicesItems.length > 0);
            },
            isDisabled() {
                return this.estimate.is_registered === 1;
            }
        },
        methods: {

            registerEstimate() {
                if (this.isShow) {
                        $('form').attr('action', '/admin/estimates/' + this.estimate.id + '/registering');
                }
            },
        }
    }
</script>
