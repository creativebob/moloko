<template>
    <li
        v-show="hasCatalogsGoods && userHasOutlet"
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
            },
            userHasOutlet() {
                return this.$store.getters.USER_HAS_OUTLET;
            }
        }
    }
</script>
