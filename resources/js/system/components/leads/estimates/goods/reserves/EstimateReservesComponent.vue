<template>
    <th
        v-if="isRegistered"
        class="th-action"
    >
        <span
            v-if="isReserved"
            @click="unreserveEstimateItems"
            class="button-to-reserve"
            title="Снять все с резерва!"
        ></span>
        <span
            v-else
            @click="reserveEstimateItems"
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
            estimate() {
                return this.$store.state.lead.estimate;
            },

            isRegistered() {
                return this.$store.state.lead.estimate.is_registered === 1;
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
            }
        },
        methods: {
            reserveEstimateItems() {
                axios
                    .post('/admin/estimates/' + this.estimate.id + '/reserving')
                    .then(response => {
                        console.log(response.data);
                        if (response.data.msg.length > 0) {
                            let msg = '';
                            response.data.msg.forEach(item => {
                                if (item !== null) {
                                    msg = msg + '- ' + item + '\r\n';
                                }
                            });
                            if (msg !== '') {
                                alert(msg);
                            }
                        }
                        this.$store.commit('UPDATE_GOODS_ITEMS', response.data.items);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            unreserveEstimateItems() {
                axios
                    .post('/admin/estimates/' + this.estimate.id + '/unreserving')
                    .then(response => {
                        console.log(response.data);
                        if (response.data.msg.length > 0) {
                            let msg = '';
                            response.data.msg.forEach(item => {
                                if (item !== null) {
                                    msg = msg + '- ' + item + '\r\n';
                                }
                            });
                            if (msg !== '') {
                                alert(msg);
                            }
                        }
                        this.$store.commit('UPDATE_GOODS_ITEMS', response.data.items);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>
