<template>
    <select
            name="stock_id"
            @change="changeStockId"
            v-model="curStockId"
    >
        <option
                v-for="stock in stocks"
                :value="stock.id"
                :selected="stockId == stock.id"
        >{{ stock.name }}</option>
    </select>
</template>

<script>
    export default {
        created: function () {
            this.$store.commit('SET_STOCK_ID', this.stockId);
        },
        props: {
            stocks: Array,
            stockId: Number
        },
        data() {
            return {
                curStockId: this.stockId,
            }
        },
        methods: {
            changeStockId() {
                this.$store.commit('SET_STOCK_ID', this.curStockId);
            }
        },
    }
</script>
