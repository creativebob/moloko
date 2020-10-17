<template>
    <th
        v-if="isRegistered"
        class="th-reserves"
    >
        <span
            v-if="isReserved"
            @click="unreserve"
            class="button-to-reserve"
            title="Снять все с резерва!"
        ></span>
        <span
            v-else
            @click="reserve"
            class="button-to-reserve"
            title="Зарезервировать все!"
        ></span>

    </th>
</template>

<script>
    export default {
        props: {
            settings: Array,
        },
        computed: {
            isRegistered() {
                return this.$store.state.lead.estimate.registered_at;
            },
            isReserved() {
                let result = [];
                result = this.$store.state.lead.goodsItems.filter(item => {
                    if (item.reserve !== null) {
                        if (item.reserve.count > 0) {
                            return item;
                        }
                    }
                });
                return result.length > 0;
            },
            isLoading() {
                return this.$store.state.lead.loading;
            }
        },
        methods: {
            reserve() {
                if (! this.isLoading) {
                    this.$store.dispatch('RESERVE_ESTIMATE');
                }
            },
            unreserve() {
                if (! this.isLoading) {
                    this.$store.dispatch('UNRESERVE_ESTIMATE');
                }
            },
        }
    }
</script>
