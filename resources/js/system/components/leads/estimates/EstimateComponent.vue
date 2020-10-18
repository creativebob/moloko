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
                <th class="th-delete"></th>

                <reserves-component
                    :settings="settings"
                ></reserves-component>

            </tr>
            </thead>
        <template v-if="goodsItems.length > 0">
            <estimates-goods-items-component
                :items="goodsItems"
                :settings="settings"
                :stocks="stocks"
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
                <td colspan="3" class="tfoot-discount-name">{{ discount.name }}</td>
                <td class="tfoot-discount-value">{{ discount.percent | decimalPlaces }}</td>
                <td class="tfoot-discount-currency"><span>{{ estimateDiscount | decimalPlaces | decimalLevel }} руб.</span></td>
                <td colspan="3" class="tfoot-discount-currency"></td>
            </tr>
            <tr v-if="discount" class="tfoot-estimate-amount">
                <td colspan="4" class="">Сумма без скидок:</td>
                <td><span>{{ estimateAmount | decimalPlaces | decimalLevel }}</span></td>
                <td colspan="2"></td>
            </tr>
            <tr v-if="discount">
                <td colspan="4" class="tfoot-estimate-discount">Скидки:</td>
                <td><span>{{ estimateItemsDiscount | decimalPlaces | decimalLevel }}</span></td>
                <td colspan="2"></td>
            </tr>

            <tr>
                <td colspan="3" class="tfoot-estimate-total">Итого к оплате:</td>
                <td></td>
                <td class="invert-show"><span>{{ estimateTotal | decimalPlaces | decimalLevel }}</span> руб.</td>
                <td colspan="3"></td>
            </tr>
            </tfoot>
        </table>

<!--        <div v-if="estimateAmount > 0">Общая стоимость: {{ estimateAmount | decimalPlaces | decimalLevel }}</div>-->
<!--        <div v-if="estimateTotalPoints > 0">Сумма поинтов: {{ estimateTotalPoints | onlyInteger | decimalLevel }}</div>-->
<!--        <div v-if="estimateItemsDiscount > 0">Сумма скидок по позициям: {{ estimateItemsDiscount | decimalPlaces | decimalLevel }}</div>-->
<!--        <div v-if="estimateDiscountCurrency > 0">{{ estimateDiscount.name}}<span v-if="estimateDiscount.mode == 1"> {{ estimateDiscount.percent | decimalPlaces | decimalLevel }}%</span>: {{ estimateDiscountCurrency | decimalPlaces | decimalLevel }}</div>-->
<!--        <div v-if="estimateTotal > 0">Итого к оплате: {{ estimateTotal | decimalPlaces | decimalLevel }}</div>-->

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
            stocks: {
                type: Array,
                default: () => {
                    return [];
                }
            },
            discount: {
                type: Object,
                default: () => {
                    return {};
                }
            },
        },
        // created() {
            // this.$store.commit('SET_ESTIMATE', this.estimate);
            // this.$store.commit('SET_GOODS_ITEMS', this.estimate.goods_items);
            // this.$store.commit('SET_SERVICES_ITEMS', this.estimate.services_items);
            // this.$store.commit('SET_DISCOUNTS', this.estimate.discounts);
        // },
		computed: {
		    // Товары
			goodsItems() {
				return this.$store.state.lead.goodsItems;
			},

            // Услуги
            servicesItems() {
                return this.$store.state.lead.servicesItems;
            },

            // Смета
            estimateAmount() {
                return this.$store.getters.estimateAmount;
            },
            estimateItemsDiscount() {
                return this.$store.getters.estimateItemsDiscount;
            },
            estimateDiscount() {
                return this.$store.getters.estimateDiscount;
            },
            estimateDiscountCurrency() {
                return this.$store.getters.estimateDiscountCurrency;
            },
            estimateTotal() {
                return this.$store.getters.estimateTotal;
            },
            estimateTotalPoints() {
                return this.$store.getters.estimateTotalPoints;
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
