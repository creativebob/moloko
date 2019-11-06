<template>

</template>

<script>
    export default {
        props: {
            estimate: Object,
        },
        created: function () {
            this.$store.commit('GET_ESTIMATE', this.estimate);
            this.$store.commit('GET_GOODS_ITEMS', this.estimate.goods_items);
        },
    }
</script>
