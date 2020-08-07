<template>
    <td>
        {{ item.price | roundToTwo | level }} {{ item.currency.abbreviation}}

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
            isRegistered: Boolean,
        },
        computed: {
            havePoints() {
                return this.item.points > 0;
            }
        },
        methods: {
            setPointsMode() {
                if(! this.isRegistered) {
                    axios
                        .patch('/admin/estimates_goods_items/' + this.item.id, {
                            sale_mode: 2,
                        })
                        .then(response => {
                            this.$emit('update', response.data);
                        })
                        .catch(error => {
                            console.log(error)
                        });
                }

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
