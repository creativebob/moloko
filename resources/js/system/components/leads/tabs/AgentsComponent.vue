<template>
    <li
        v-show="isRegistered && hasAgentSetting && oneCatalog"
        class="tabs-title">
        <a data-tabs-target="tab-agents" href="#tab-agents">Агенты</a>
    </li>
</template>

<script>
    export default {
        computed: {
            isRegistered() {
                return this.$store.state.lead.estimate.registered_at !== null;
            },
            hasAgentSetting() {
                return this.$store.getters.HAS_OUTLET_SETTING('agents')
            },
            oneCatalog() {
                return this.$store.state.lead.catalogsGoodsIds.length == 1;
            }
        }
    }
</script>
