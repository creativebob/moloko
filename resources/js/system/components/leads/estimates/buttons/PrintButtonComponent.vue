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

        data() {
            return {
                estimate: this.$store.state.lead.estimate,
                leadId: this.$store.state.lead.estimate.lead_id
            }
        },
        computed: {
            isShow() {
                return this.estimate.is_registered === 1;
            },
        },
    }
</script>
