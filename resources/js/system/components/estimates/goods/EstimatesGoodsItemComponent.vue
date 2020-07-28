<template>
    <tr
        class="item commentable"
        :id="'estimates_goods_items-' + item.id"
        :data-name="item.product.article.name"
        :data-price_id="item.price_id"
        :data-count="item.count"
        :data-price="item.price"
    >

        <comment-component
            :item="item"
        ></comment-component>

<!--        <td v-if="settings.length && stocks.length">-->
<!--            <select-->
<!--                name="stock_id"-->
<!--                v-model="stockId"-->
<!--            >-->
<!--                <option v-for="stock in stocks"-->
<!--                    :value="stock.id"-->
<!--                >{{ stock.name }}</option>-->
<!--            </select>-->
<!--        </td>-->
<!--        <td v-else>-->
<!--            {{ item.stock.name }}-->
<!--        </td>-->

        <td>{{ item.price | roundToTwo | level }} <span v-if="item.price_goods.point > 0">({{ item.price_goods.point }})</span></td>

        <td @click="checkChangeCount">
            <template v-if="isChangeCount">
                <input
                    @keydown.enter.prevent="updateItemCount"
                    type="number"
                    v-focus
                    @focusout="canChangeCount = false"

                >
            </template>
            <template v-else>{{ item.count | roundToTwo | level }}</template>
        </td>

        <td class="td-discount">
            <template
                v-if="item.discount_percent > 0"
            >{{ item.discount_percent | roundToTwo | level }} %</template>
        </td>

        <td class="td-amount">
            <a
                class="button green-button"
                :data-open="'modal-estimates_goods_item-' + item.id"
            >{{ item.total | roundToTwo | level }}</a>
        </td>
        <td class="td-delete">
            <div
                v-if="!isRegistered"
                @click="openModalRemoveItem"
                class="icon-delete sprite"
                data-open="delete-estimates_goods_item"
            ></div>
        </td>
        <td
            v-if="settings.length && isRegistered"
            class="td-action"
        >
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
        </td>

        <modal-component
            :item="item"
            :is-registered="isRegistered"
        ></modal-component>
    </tr>
</template>

<script>
    export default {
        components: {
            'comment-component': require('./CommentComponent'),
            'modal-component': require('./ModalSettingsComponent'),
            'digit-component': require('../../inputs/DigitComponent')
        },
        props: {
            item: Object,
            index: Number,
            isRegistered: Boolean,
            settings: Array,
            stocks: Array,
        },
        data() {
            return {
                countInput: Number(this.item.count),
                cost: Number(this.item.cost),
                canChangeCount: false,
                changeCost: false,
                stockId: null,
            }
        },
        mounted() {
            if(this.settings.length && this.stocks.length && this.item.stock_id === null) {
                this.stockId = this.stocks[0].id;
            } else {
                this.stockId = this.item.stock_id;
            }
        },
        computed: {
            estimate() {
                return this.$store.state.estimate.estimate;
            },
            isChangeCount() {
                return this.canChangeCount
            },
            // count: {
            //     get () {
            //         return Number(this.item.count);
            //     },
            //     set (value) {
            //         this.countInput = Number(value)
            //     }
            // },
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
            //     isChangeCost() {
            //         if (this.changeCost) {
            //             this.canChangeCount = false
            //         }
            //         return this.changeCost
            //     },
            //     unitAbbreviation() {
            //         let abbr;
            //         if (this.item.cmv.article.package_status === 1) {
            //             abbr = this.item.cmv.article.package_abbreviation;
            //         } else {
            //             abbr = this.item.cmv.article.unit.abbreviation;
            //         }
            //         return abbr;
            //     }
            //
            total() {
                return (this.item.price - this.discountCurrency) * this.itemCount;
            },
            itemCount() {
                return Math.floor(this.item.count);
            }
        },
        methods: {
            changeCount(value) {
                this.item.count = value;
            },
            openModalRemoveItem() {
                this.$emit('open-modal-remove', this.item);
            },
            checkChangeCount() {
                if (this.item.product.serial === 0) {
                    if (!this.isRegistered) {
                        this.canChangeCount = !this.canChangeCount
                    }
                }
            },
            // checkChangeCost() {
            //     if (!this.isRegistered) {
            //         this.changeCost = !this.changeCost
            //     }
            // },
            updateItemCount: function() {
                this.canChangeCount = false;
                // this.changeCost = false;
                axios
                    .patch('/admin/estimates_goods_items/' + this.item.id, {
                        count: Number(this.countInput),
                        // cost: Number(this.cost)
                    })
                    .then(response => {
                        this.$emit('update', response.data);
                        this.countInput = Number(response.data.count);
                        // this.cost = Number(response.data.cost);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            // deleteItem: function() {
            //     axios
            //         .delete('/admin/consignments_items/' + this.item.id)
            //         .then(response => {
            //             if(response.data > 0) {
            //                 this.$emit('remove');
            //             }
            //         })
            //         .catch(error => {
            //             console.log(error)
            //         });
            // },
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

        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
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
