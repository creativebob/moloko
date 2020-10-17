<template>
    <div
        :class="isReservedClass"
    >
        <span
            v-if="!isReserved"
            @click="reserving"
            class="button-to-reserve"
            title="Позицию в резерв!"
        ></span>
        <span
            v-else
            @click="unreserving"
            class="button-to-reserve unreserve"
            title="Снять с резерва!"
        ></span>
        <span
            v-if="reservedCount > 0"
            class="reserved-count"
        >{{ reservedCount | onlyInteger | level }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            reserve: Object,
        },
        computed: {
            isReservedClass() {
                if (this.reserve !== null) {
                    if (this.reserve.count > 0) {
                        return 'wrap-reserved-info active';
                    }
                }
                return 'wrap-reserved-info';
            },
            isReserved() {
                if (this.reserve !== null) {
                    if (this.reserve.count > 0) {
                        return true;
                    }
                }
                return false;
            },
            reservedCount() {
                if (this.reserve !== null) {
                    if (this.reserve.count > 0) {
                        return this.reserve.count;
                    }
                }
                return 0;
            },
            isLoading() {
                return this.$store.state.lead.loading;
            }
        },
        methods: {
            reserving() {
                if (! this.isLoading) {
                    this.$emit('reserve');
                }

            },
            unreserving() {
                if (! this.isLoading) {
                    this.$emit('unreserve');
                }
            },
        },
        filters: {
            level: function (value) {
                return parseInt(value).toLocaleString();
            },
            // Отбраcывает дробную часть в строке с числами
            onlyInteger(value) {
                return Math.floor(value);
            },
        }
    }
</script>
