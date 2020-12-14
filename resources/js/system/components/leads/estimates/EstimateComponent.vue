<template>
    <div>
        <table
            v-if="goodsItems.length > 0 || servicesItems.length > 0"
            class="table-estimate lead-estimate"
            id="table-estimate_goods_items"
        >

            <thead>
            <tr>
                <th>Наименование</th>
                <th>Цена</th>
                <th>Кол-во</th>
                <th class="td-discount">Скидка</th>
                <th class="th-amount">Сумма</th>
                <th
                    v-if="!isRegistered"
                    class="th-delete"
                ></th>

                <reserves-component
                    v-if="canReserve"
                    :settings="settings"
                ></reserves-component>

            </tr>
            </thead>
            <template v-if="goodsItems.length > 0">
                <estimates-goods-items-component
                    :items="goodsItems"
                    :settings="settings"
                ></estimates-goods-items-component>
            </template>

            <template v-if="servicesItems.length > 0">
                <estimates-services-items-component
                    :items="servicesItems"
                    :settings="settings"
                ></estimates-services-items-component>
            </template>
            <tfoot>

            <tr v-if="discount" class="tfoot-discount-info">
                <td colspan="3" class="tfoot-discount-name">{{ discount.name }} <span v-if="!isActual">(Архивная)</span></td>
                <td class="tfoot-discount-value">{{ discount.percent | decimalPlaces }}</td>
                <td class="tfoot-discount-currency total-estimate-tfoot"><span>{{ estimateAggregations.estimate.discount | decimalPlaces | decimalLevel }} руб.</span>
                </td>
<!--                <td class="tfoot-discount-currency"></td>-->
                <td class="td-delete">
                    <div
                        v-if="!isRegistered && !isActual"
                        class="icon-delete sprite"
                        @click="removeDiscount(discount.id)"
                    ></div>
                </td>
            </tr>

            <template
                v-if="estimateAggregations.estimate.itemsDiscount > 0"
            >
                <tr class="tfoot-estimate-amount">
                    <td colspan="4" class="">Сумма без скидок:</td>
                    <td class="total-estimate-tfoot" colspan="2"><span>{{ estimateAggregations.estimate.amount | decimalPlaces | decimalLevel }}</span></td>
                </tr>
                <tr class="tfoot-estimate-discount">
                    <td colspan="4" class="tfoot-estimate-discount">Скидки:</td>
                    <td class="total-estimate-tfoot" colspan="2"><span>{{ estimateAggregations.estimate.itemsDiscount | decimalPlaces | decimalLevel }}</span>
                    </td>
                </tr>
            </template>

            <tr class="tfoot-estimate-total">
                <td colspan="4">Итого к оплате:</td>
                <td class="total-estimate-tfoot" colspan="2">
                    <span>{{ estimateAggregations.estimate.total | decimalPlaces | decimalLevel }}</span> руб.
                </td>
            </tr>
            </tfoot>
        </table>

        <buttons-component></buttons-component>
    </div>
</template>

<script>
export default {
    components: {
        'reserves-component': require('./goods/reserves/ReservesComponent'),
        'estimates-goods-items-component': require('./goods/EstimatesGoodsItemsComponent'),
        'estimates-services-items-component': require('./services/EstimatesServicesItemsComponent'),
        'buttons-component': require('./buttons/ButtonsComponent'),
    },
    props: {
        estimate: Object,
        settings: {
            type: Array,
            default: () => {
                return [];
            }
        },
        // stocks: {
        //     type: Array,
        //     default: () => {
        //         return [];
        //     }
        // },
    },
    mounted() {
        this.$store.commit('SET_AGENT', this.estimate.agent);
    },
    // created() {
    // this.$store.commit('SET_ESTIMATE', this.estimate);
    // this.$store.commit('SET_GOODS_ITEMS', this.estimate.goods_items);
    // this.$store.commit('SET_SERVICES_ITEMS', this.estimate.services_items);
    // this.$store.commit('SET_DISCOUNTS', this.estimate.discounts);
    // },
    computed: {
        // Смета
        isRegistered() {
            return this.$store.state.lead.estimate.registered_at != null;
        },
        estimateAggregations() {
            return this.$store.getters.ESTIMATE_AGGREGATIONS;
        },

        // Скидка
        discount() {
            if (this.$store.state.lead.estimate.discounts.length) {
                return this.$store.state.lead.estimate.discounts[0];
            } else {
                return null;
            }
        },
        isActual() {
            if (this.$store.state.lead.estimate.discounts.length) {
                return this.$store.state.lead.estimate.discounts[0].is_actual == 1;
            } else {
                return false;
            }
        },

        // Товары
        goodsItems() {
            return this.$store.state.lead.goodsItems;
        },

        canReserve() {
            return this.$store.getters.HAS_OUTLET_SETTING('reserves');
        },

        // Услуги
        servicesItems() {
            return this.$store.state.lead.servicesItems;
        },
    },
    methods: {
        removeDiscount(id) {
            this.$store.commit('REMOVE_DISCOUNT', id);
        },

    },
    filters: {
        decimalPlaces(value) {
            return parseFloat(value).toFixed(2);
        },
        decimalLevel: function (value) {
            return parseFloat(value).toLocaleString();
        },
        onlyInteger(value) {
            return Math.floor(value);
        },
    },
}
</script>
