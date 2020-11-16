<template>
    <li
        v-if="hasCatalogsServices"
        class="tabs-title"
    >
        <a data-tabs-target="tab-catalog_services" href="#tab-catalog_services">Услуги</a>
    </li>
</template>

<script>
    export default {
        computed: {
            hasCatalogsServices() {
                return this.$store.state.lead.outlet.catalogs_services.length > 0
            }
        }
    }
</script>
