<template>

</template>

<script>
    export default {
        props: {
            outlet: Object,
        },
        mounted() {
            let stock = this.outlet.stock;
            if (! stock.id) {
                stock.id = null;
            }
            this.$store.commit('SET_STOCK', stock);
            this.$store.commit('SET_OUTLET_SETTINGS', this.outlet.settings);
            // this.$store.commit('SET_USERS', this.users);
            // this.$store.commit('SET_COMPANIES', this.companies);
            //
            // this.$store.commit('SET_LEAD', this.lead);
            // this.$store.commit('SET_CLIENT', this.lead.client);
            //
            // this.$store.commit('SET_ESTIMATE', this.lead.estimate);
            // this.$store.commit('SET_GOODS_ITEMS', this.lead.estimate.goods_items);
            // this.$store.commit('SET_SERVICES_ITEMS', this.lead.estimate.services_items);
        },
    }
</script>
