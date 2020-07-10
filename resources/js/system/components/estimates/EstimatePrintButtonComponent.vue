<template>
    <a
        :href="'/admin/estimates/' + estimate.id + '/print'"
        target="_blank"
        class="button"
        v-if="isShow"
    >Печать</a>
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
                return this.estimate.is_saled === 1;
            },
        },
    }
</script>
