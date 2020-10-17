<template>
    <td>
        {{ item.price | roundToTwo | level }} <span class="item-currency">{{ item.currency.abbreviation}}</span>
        <template
            v-if="havePoints"
        >
            <span
                v-if="isRegistered"
                class="points"
            >({{ item.points }})</span>
            <span
                class="points points-mode"
                v-else
                @click="setPointsMode"
            >({{ item.points }})</span>
        </template>

    </td>
</template>

<script>
    export default {
        props: {
            item: Object,
        },
        computed: {
            isRegistered() {
                return this.$store.state.lead.estimate.registered_at;
            },
            havePoints() {
                return this.item.points > 0;
            }
        },
        methods: {
            setPointsMode() {
                this.item.sale_mode = 2;
                this.$emit('update', this.item);
            }
        },
        filters: {
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },
            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return parseInt(value).toLocaleString();
            },

            // Отбраcывает дробную часть в строке с числами
            onlyInteger(value) {
                return Math.floor(value);
            },
        },
    }
</script>
