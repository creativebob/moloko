<template>
    <tr
        class="item commentable"
        :class="[{'process-archive' : isArchive}]"
        :id="'estimates_services_items-' + item.id"
    >

        <td class="td-name">
            {{ item.service.process.name }}<span v-if="isArchive"> (Архивный)</span>
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
                v-if="isRegistered || this.item.service.serial === 1"
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
                :data-open="'modal-estimates_services_item-' + item.id"
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
                data-open="delete-estimates_services_item"
            ></div>
        </td>

        <td
            class="td-flow"
        >
            <template
                v-if="item.service.process.is_auto_initiated == 0"
            >
                <select
                    v-if="!isRegistered"
                    @change="changeFlow($event.target.value)"
                >
                    <option
                        v-for="flow in item.service.actual_flows"
                        :value="flow.id"
                        :selected="flow.id == item.flow_id"
                    >{{ flow.start_at | formatDate }} - {{ flow.finish_at | formatDate }}</option>
                </select>
                <span v-else>{{ item.flow.start_at | formatDate }} - {{ item.flow.finish_at | formatDate }}</span>
            </template>

        </td>

        <modal-component
            :id="id"
            ref="modalCurrencyComponent"
            @update="update"
        ></modal-component>
    </tr>
</template>

<script>
import moment from 'moment'

export default {
    components: {
        'comment-component': require('./CommentComponent'),
        'currency-component': require('./price/CurrencyComponent'),
        'points-component': require('./price/PointsComponent'),
        'count-component': require('../../../inputs/CountWithButtonsComponent'),
        'modal-component': require('./ModalCurrencyComponent'),
        'digit-component': require('../../../inputs/DigitComponent'),
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
    created() {
        if (this.item.service.process.is_auto_initiated == 0) {
            if (this.item.service.actual_flows.length && !this.item.flow_id) {
                this.changeFlow(this.item.service.actual_flows[0].id)
            }
        } else {
            this.changeFlow()
        }
    },
    computed: {
        item() {
            return this.$store.getters.SERVICE_ITEM(this.id);
        },
        isRegistered() {
            return this.$store.state.lead.estimate.registered_at;
        },
        isConducted() {
            return this.$store.state.lead.estimate.conducted_at;
        },
        isArchive() {
            return this.item.service.archive == 1;
        },
        hasComment() {
            return this.item.comment !== null && this.item.comment !== "";
        },
    },
    methods: {
        changeComment(comment) {
            // Оновление ккомментария
            let data = {
                id: this.id,
                comment: comment
            };
            this.$store.commit('UPDATE_SERVICE_ITEM_COMMENT', data)
        },
        changeCount(count) {
            // Оновление количества из строки
            // this.item.count = count;
            const data = {
                id: this.id,
                count: count
            };
            this.$store.commit('UPDATE_SERVICE_ITEM_COUNT', data);
            this.$refs.modalCurrencyComponent.reset();
        },
        changeFlow(flowId = null) {
            const data = {
                id: this.id,
                flowId: flowId
            };
            this.$store.commit('UPDATE_SERVICE_ITEM_FLOW', data);
        },
        update(data) {
            // Обновление из модалки
            this.$store.commit('UPDATE_SERVICE_ITEM_IS_MANUAL', data)
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
                this.$store.dispatch('REMOVE_SERVICE_ITEM_FROM_ESTIMATE', item.remove_from_page);
                // this.$refs.countComponent.setCount(item.count);
            }
            this.$store.commit('UPDATE_SERVICE_ITEM', item)
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

        formatDate: function (value) {
            if (value) {
                return moment(String(value)).format('DD.MM.YYYY')
            }
        },
    },
}
</script>
