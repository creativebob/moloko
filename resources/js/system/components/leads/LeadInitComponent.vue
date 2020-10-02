<template>

</template>

<script>
    export default {
        props: {
            lead: Object,
        },
        created: function () {
            this.$store.commit('SET_LEAD', this.lead);
            this.$store.commit('SET_CLIENT', this.lead.client);
            this.$store.commit('SET_ESTIMATE', this.lead.estimate);
            this.$store.commit('SET_GOODS_ITEMS', this.lead.estimate.goods_items);
            // this.$store.commit('SET_SERVICES_ITEMS', this.lead.estimate.services_items);
        },
    }
</script>
