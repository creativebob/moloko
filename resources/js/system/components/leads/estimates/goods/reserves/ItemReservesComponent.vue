<template>
    <div
        :class="isReservedClass"
    >
                <span
                    v-if="!isReserved"
                    @click="reserveEstimateItem"
                    class="button-to-reserve"
                    title="Позицию в резерв!"
                ></span>
        <span
            v-else
            @click="unreserveEstimateItem"
            class="button-to-reserve unreserve"
            title="Снять с резерва!"
        ></span>
        <span
            v-if="reservedCount > 0"
            class="reserved-count"
        >{{ reservedCount | roundToTwo | level }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            item: Object,
        },
        computed: {
            isReservedClass() {
                if (this.item.reserve !== null) {
                    if (this.item.reserve.count > 0) {
                        return 'wrap-reserved-info active';
                    }
                }
                return 'wrap-reserved-info';
            },
            isReserved() {
                if (this.item.reserve !== null) {
                    if (this.item.reserve.count > 0) {
                        return true;
                    }
                }
                return false;
            },
            reservedCount() {
                if (this.item.reserve !== null) {
                    if (this.item.reserve.count > 0) {
                        return this.item.reserve.count;
                    }
                }
                return 0;
            },
        },
        methods: {
            reserveEstimateItem() {
                axios
                    .post('/admin/estimates_goods_items/' + this.item.id + '/reserving')
                    .then(response => {
                        if (response.data.msg !== null) {
                            alert(response.data.msg);
                        }
                        this.$emit('update', response.data.item);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            unreserveEstimateItem() {
                axios
                    .post('/admin/estimates_goods_items/' + this.item.id + '/unreserving')
                    .then(response => {
                        if (response.data.msg !== null) {
                            alert(response.data.msg);
                        }
                        this.$emit('update', response.data.item);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>
