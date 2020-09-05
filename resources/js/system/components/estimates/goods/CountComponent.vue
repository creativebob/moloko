<template>
    <td>
        <template v-if="canChangeCount">
            <template
                v-if="changeCount"
            >
                <digit-component
                    :value="curCount"
                    @change="setCount"
                    :blur="true"
                    @blur="changeCount = false"
                    :enter="true"
                    @enter="updateCount"
                    :decimal-place="0"
                    ref="inputComponent"
                ></digit-component>
                <!--            <input-->
                <!--                @keydown.enter.prevent="updateItemCount"-->
                <!--                type="number"-->
                <!--                v-focus-->
                <!--                @focusout="canChangeCount = false"-->
                <!--            >-->
            </template>
            <template
                v-else
            >
                <span
                    @click="changeCount = true"
                >{{ item.count | onlyInteger | level }}</span>
            </template>

        </template>
        <template
            v-else
        >
            <span>{{ item.count | onlyInteger | level }}</span>
        </template>
    </td>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../inputs/DigitComponent')
        },
        props: {
            item: Object,
        },
        data() {
            return {
                changeCount: false,
                curCount: this.item.count,
            }
        },
        computed: {
            canChangeCount() {
                return this.item.product.serial === 0 && ! this.isRegistered;
            },
            isRegistered() {
                return this.$store.state.estimate.estimate.is_registered == 1;
            }
        },
        methods: {
            setCount(value) {
                this.curCount = value;
            },
            updateCount() {
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        count: parseInt(this.curCount),
                    })
                    .then(response => {
                        this.$store.commit('UPDATE_GOODS_ITEM', response.data);
                        this.curCount = parseInt(response.data.count);
                        this.$emit('update', this.curCount);
                        this.changeCount = false;
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            changeValue(value) {
                this.item.count = value;
                this.curCount = value;
                this.$refs.inputComponent.update(value);
            },
        },
        filters: {
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
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
