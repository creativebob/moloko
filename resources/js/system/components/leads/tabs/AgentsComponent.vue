<template>
    <li
        v-show="isRegistered && hasAgentSetting && oneCatalog && userHasOutlet"
        class="tabs-title"
    >
        <a data-tabs-target="tab-agents" href="#tab-agents">Агенты</a>
    </li>
</template>

<script>
    export default {
        computed: {
            isRegistered() {
                return this.$store.getters.IS_REGISTERED;
            },
            hasAgentSetting() {
                return this.$store.getters.HAS_OUTLET_SETTING('agents');
            },
            oneCatalog() {
                return this.$store.state.lead.catalogsGoodsIds.length == 1;
            },
            userHasOutlet() {
                return this.$store.getters.USER_HAS_OUTLET;
            }
        }
    }
</script>
