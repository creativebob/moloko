<template>
    <li
        v-show="isShow"
        class="tabs-title">
        <a data-tabs-target="tab-payments" href="#tab-payments">Оплата</a>
    </li>
</template>

<script>
    export default {
        computed: {
            isShow() {
                return this.$store.state.lead.estimate.registered_at;
            }
        }
    }
</script>
