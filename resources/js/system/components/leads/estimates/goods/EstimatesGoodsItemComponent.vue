<template>
    <tr
        class="item commentable"
        :class="[{'cmv-archive' : isArchive}]"
        :id="'estimates_goods_items-' + item.id"
    >

        <td class="td-name">
            {{ item.goods.article.name }}<span v-if="isArchive"> (Архивный)</span>
            <template
                v-if="isRegistered"
            >
                <span
                    v-if="item.comment"
                    class="icon-comment"
                    data-tooltip
                    tabindex="1"
                    :title="item.comment"
                ></span>
            </template>

            <comment-component
                v-else
                :item="item.comment"
                @update="changeComment"
            ></comment-component>
        </td>

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
                @update="updatePrice"
            ></currency-component>
        </template>
        <template
            v-else
        >
            <points-component
                :item="item"
                :is-registered="isRegistered"
                @update="updatePrice"
            ></points-component>
        </template>

        <td class="td-count">
            <span
                v-if="isRegistered || this.item.goods.serial === 1"
            >{{ item.count | onlyInteger | level }}</span>
            <count-component
                v-else
                :count="item.count"
                @update="changeCount"
                ref="countComponent"
            ></count-component>
        </td>

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
                @click="openModalRemove"
                class="icon-delete sprite"
                data-open="delete-estimates_goods_item"
            ></div>
        </td>

        <td
            v-if="settings.length && isRegistered"
            class="td-action"
        >
            <reserves-component
                :item="item"
            ></reserves-component>
        </td>

        <modal-component
            :item="item"
            ref="modalCurrencyComponent"
            @update="update"
        ></modal-component>
    </tr>
</template>

<script>
    export default {
        components: {
            'comment-component': require('./CommentComponent'),
            'currency-component': require('./price/CurrencyComponent'),
            'points-component': require('./price/PointsComponent'),
            'count-component': require('../../../inputs/CountWithButtonsComponent'),
            'modal-component': require('./ModalCurrencyComponent'),
            'digit-component': require('../../../inputs/DigitComponent'),
            'reserves-component': require('./reserves/ItemReservesComponent'),
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
                count: parseFloat(this.item.count),
                stockId: null,

                // cost: Number(this.item.cost),
                // changeCost: false,
            }
        },
        // watch: {
        //     count: ((val, oldVal) => {
        //         if (val != oldVal) {
        //             alert(val);
        //         }
        //     })
        // },
        mounted() {
            if (this.settings.length && this.stocks.length && this.item.stock_id === null) {
                this.stockId = this.stocks[0].id;
            } else {
                this.stockId = this.item.stock_id;
            }
        },
        computed: {
            isArchive() {
                return this.item.goods.archive == 1;
            },
            isRegistered() {
                return this.$store.state.lead.estimate.is_registered == 1;
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
            changeComment(comment) {
                // Оновление ккомментария
                this.item.comment = comment;
                this.$store.commit('UPDATE_GOODS_ITEM', this.item)
            },
            changeCount(count) {
                // Оновление количества из строки
                this.item.count = count;
                this.$store.commit('UPDATE_GOODS_ITEM', this.item);
                this.$refs.modalCurrencyComponent.reset();
            },
            update(item) {
                // Обновление из модалки
                this.$store.commit('UPDATE_GOODS_ITEM', item)
            },
            openModalRemove() {
                // Открытие модалки удаления
                this.$emit('open-modal-remove', this.item);
            },
            updatePrice(item) {
                // Обновление редима оплаты (валюта / поинты)
                if (item.sale_mode == 2) {
                    this.$refs.modalCurrencyComponent.reset();
                }

                if (item.remove_from_page) {
                    this.$store.dispatch('REMOVE_GOODS_ITEM_FROM_ESTIMATE', item.remove_from_page);
                    // this.$refs.countComponent.setCount(item.count);
                }
                this.$store.commit('UPDATE_GOODS_ITEM', item)
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
