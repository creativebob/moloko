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

            <span
                v-else
                :class="[{'comment' : hasComment}]"
            >
                <comment-component
                    :comment="item.comment"
                    @update="changeComment"
                ></comment-component>
            </span>

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
                :limit-min="1"
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
                class="button green-button open-modal-estimate-item"
                :data-open="'modal-estimates_goods_item-' + item.id"
            >{{ item.total | decimalPlaces | decimalLevel }}</a>
            <a
                v-else
                class="button green-button open-modal-estimate-item"
            >{{ item.total_points | level }} поинтов</a>
        </td>

        <td
            v-if="!isRegistered"
            class="td-delete"
        >
            <div

                @click="openModalRemove"
                class="icon-delete sprite"
                data-open="delete-estimates_goods_item"
            ></div>
        </td>

        <td
            v-if="isRegistered && !isConducted && canReserve"
            class="td-reserve"
        >
            <reserve-component
                :reserve="item.reserve"
                @reserve="reserve"
                @cancel="cancelReserve"
            ></reserve-component>
        </td>

        <modal-component
            :id="id"
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
            'reserve-component': require('./reserves/ReserveComponent'),
        },
        props: {
            id: Number,
            // item: Object,
            index: Number,
            settings: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            // stock: Object,
        },
        data() {
            return {
                // count: parseFloat(this.item.count),
                // stockId: null,

            }
        },
        // watch: {
        //     count: ((val, oldVal) => {
        //         if (val != oldVal) {
        //             alert(val);
        //         }
        //     })
        // },
        // mounted() {
        //     if (this.settings.length && this.stock.id && this.item.stock_id === null) {
        //         this.stockId = this.stock.id;
        //     } else {
        //         this.stockId = this.item.stock_id;
        //     }
        // },
        computed: {
            item() {
                return this.$store.getters.GOODS_ITEM(this.id);
            },
            isRegistered() {
                return this.$store.state.lead.estimate.registered_at;
            },
            isConducted() {
                return this.$store.state.lead.estimate.conducted_at;
            },
            isArchive() {
                return this.item.goods.archive == 1;
            },
            hasComment() {
                return this.item.comment !== null && this.item.comment !== "";
            },
            canReserve() {
                return this.$store.getters.HAS_OUTLET_SETTING('reserves');
            },
        },
        methods: {
            changeComment(comment) {
                // Оновление ккомментария
                let data = {
                    id: this.id,
                    comment: comment
                };
                this.$store.commit('UPDATE_GOODS_ITEM_COMMENT', data)
            },
            changeCount(count) {
                // Оновление количества из строки
                // this.item.count = count;
                const data = {
                    id: this.id,
                    count: count
                };
                this.$store.commit('UPDATE_GOODS_ITEM_COUNT', data);
                this.$refs.modalCurrencyComponent.reset();
            },
            update(data) {
                // Обновление из модалки
                this.$store.commit('UPDATE_GOODS_ITEM_IS_MANUAL', data)
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
            reserve() {
                this.$store.dispatch('RESERVE_GOODS_ITEM', this.item.id);
            },
            cancelReserve() {
                this.$store.dispatch('CANCEL_RESERVE_GOODS_ITEM', this.item.id);
            }
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
