<template>
    <a
        :href="'/admin/leads/' + leadId + '/print'"
        target="_blank"
        class="button"
        v-if="isShow"
    >Печать</a>
</template>

<script>
    export default {
        props: {
            leadId: Number
        },
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
