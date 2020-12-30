<template>
    <li
        v-if="hasCatalogsServices && userHasOutlet"
        class="tabs-title"
    >
        <a data-tabs-target="tab-catalog_services" href="#tab-catalog_services" role="tab">Услуги</a>
    </li>
</template>

<script>
    export default {
        computed: {
            hasCatalogsServices() {
                return this.$store.state.lead.outlet.catalogs_services.length > 0
            },
            userHasOutlet() {
                return this.$store.getters.USER_HAS_OUTLET;
            }
        }
    }
</script>
