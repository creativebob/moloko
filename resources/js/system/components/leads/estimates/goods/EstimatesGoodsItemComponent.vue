<template>
    <tr
        class="item commentable"
        :class="[{'cmv-archive' : isArchive}]"
        :id="'estimates_goods_items-' + item.id"
        :data-name="item.product.article.name"
        :data-price_id="item.price_id"
        :data-count="item.count"
        :data-price="item.price"
    >
        <comment-component
            :item="item"
            :is-archive="isArchive"
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

        <template
            v-if="item.sale_mode == 1"
        >
            <currency-component
                :item="item"
                :is-registered="isRegistered"
                @update="updateItem"
            ></currency-component>
        </template>
        <template
            v-else
        >
            <points-component
                :item="item"
                :is-registered="isRegistered"
                @update="updateItem"
            ></points-component>
        </template>

        <count-component
            :item="item"
            @update="updateModalCount"
            ref="countComponent"
        ></count-component>

        <td class="td-discount">
            <template
                v-if="item.discount_percent > 0"
            >
                {{ item.discount_percent | decimalPlaces | decimalLevel }}
                <span class="percent-symbol">%</span>
            </template>
        </td>

        <td class="td-total">
            <a
                v-if="item.sale_mode == 1"
                class="button green-button"
                :data-open="'modal-estimates_goods_item-' + item.id"
            >{{ item.total | decimalPlaces | decimalLevel }}</a>
            <a
                v-else
                class="button green-button"
            >{{ item.total_points | level }} поинтов</a>
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
            ref="modalCurrencyComponent"
            @update-count="updateCount"
        ></modal-component>
    </tr>
</template>

<script>
    export default {
        components: {
            'comment-component': require('./CommentComponent'),
            'currency-component': require('./price/CurrencyComponent'),
            'points-component': require('./price/PointsComponent'),
            'count-component': require('./CountComponent'),
            'modal-component': require('./ModalCurrencyComponent'),
            'digit-component': require('../../../inputs/DigitComponent')
        },
        props: {
            item: Object,
            index: Number,
            settings: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            stocks: {
                type: Array,
                default: () => {
                    return [];
                }
            },
        },
        data() {
            return {
                countInput: parseFloat(this.item.count),
                stockId: null,

                // cost: Number(this.item.cost),
                // changeCost: false,
            }
        },
        mounted() {
            if (this.settings.length && this.stocks.length && this.item.stock_id === null) {
                this.stockId = this.stocks[0].id;
            } else {
                this.stockId = this.item.stock_id;
            }
        },
        computed: {
            isArchive() {
                return this.item.product.archive == 1;
            },
            isRegistered() {
                return this.$store.state.lead.estimate.is_registered == 1;
            },
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
            itemCount() {
                return Math.floor(this.item.count);
            }
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
            // count: {
            //     get () {
            //         return Number(this.item.count);
            //     },
            //     set (value) {
            //         this.countInput = Number(value)
            //     }
            // },
        },
        methods: {
            updateModalCount(count) {
                if (this.item.sale_mode == 1) {
                    this.$refs.modalCurrencyComponent.update(count);
                }
            },
            updateCount(count) {
                if (this.item.sale_mode == 1) {
                    this.$refs.countComponent.update(count);
                }
            },
            openModalRemoveItem() {
                this.$emit('open-modal-remove', this.item);
            },
            updateItem(item) {
                if (item.sale_mode == 2) {
                    this.$refs.modalCurrencyComponent.reset();
                }

                if (item.remove_from_page) {
                    this.$store.dispatch('REMOVE_GOODS_ITEM_FROM_ESTIMATE', item.remove_from_page);
                    // this.$refs.countComponent.setCount(item.count);
                }
                this.$emit('update', item);
            },
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
