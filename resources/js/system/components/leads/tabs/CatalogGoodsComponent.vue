<template>
    <li
        v-if="hasCatalogsGoods"
        class="tabs-title"
    >
        <a data-tabs-target="tab-catalog_goods" href="#tab-catalog_goods">Товары</a>
    </li>
</template>

<script>
    export default {
        computed: {
            hasCatalogsGoods() {
                return this.$store.state.lead.outlet.catalogs_goods.length > 0
            }
        }
    }
</script>
