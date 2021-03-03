<template>
    <ul
        class="tabs-list"
        data-tabs
        id="tabs-extra-leads"
    >
        <li class="tabs-title is-active">
            <a href="#tab-estimate" aria-selected="true">Состав заказа</a>
        </li>

        <li
            v-if="canIndexClient"
            class="tabs-title"
        >
            <a data-tabs-target="content-panel-client" href="#content-panel-client">Карточка клиента</a>
        </li>

<!--        <li class="tabs-title" id="tab-address"><a href="#content-panel-address" aria-selected="true">Адреса</a></li>-->

        <li
            v-if="canLeadHistory"
            class="tabs-title"
        >
            <a data-tabs-target="tab-history" href="#tab-history">История</a>
        </li>

        <li class="tabs-title">
            <a data-tabs-target="tab-options" href="#tab-options">Опции</a>
        </li>
    </ul>
</template>

<script>
export default {
    props: {
        canIndexClient: {
            type: Boolean,
            default: false
        },
        canLeadHistory: {
            type: Boolean,
            default: false
        },
    },
    computed: {
        needChangeTabToEstimate() {
            return this.$store.state.lead.needChangeTabToEstimate;
        }
    },
    watch: {
        needChangeTabToEstimate(val) {
            if(val) {
                this.$store.commit('CHANGE_NEED_CHANGE_TAB_TO_ESTIMATE');
                $('#tabs-extra-leads').foundation('selectTab', $('#tab-estimate'), false);
            }
        }
    },
}
</script>
