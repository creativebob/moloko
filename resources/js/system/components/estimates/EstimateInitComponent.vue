<template>

</template>

<script>
    export default {
        props: {
            estimate: Object,
        },
        created: function () {
            this.$store.commit('SET_ESTIMATE', this.estimate);
            this.$store.commit('SET_GOODS_ITEMS', this.estimate.goods_items);
            this.$store.commit('SET_SERVICES_ITEMS', this.estimate.services_items);
        },
    }
</script>
