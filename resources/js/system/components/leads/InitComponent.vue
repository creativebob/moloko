<template>

</template>

<script>
export default {
    props: {
        outlet: Object,
    },
    created() {
        let stock;
        if (this.outlet.stock_id) {
            stock = this.outlet.stock;
        } else {
            stock = {
                id: null
            };
        }
        this.$store.commit('SET_STOCK', stock);
        this.$store.commit('SET_OUTLET', this.outlet);
        this.$store.commit('SET_OUTLET_SETTINGS', this.outlet.settings);
        this.$store.commit('SET_PAYMENTS_METHODS', this.outlet.payments_methods);

        this.$store.commit('SET_CATALOG_GOODS_ID', this.outlet.catalogs_goods.length ? this.outlet.catalogs_goods[0].id : null);

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
