<template>
    <li
        v-show="isShow"
        class="tabs-title">
        <a href="#tab-payments" aria-selected="true">Оплата</a>
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
