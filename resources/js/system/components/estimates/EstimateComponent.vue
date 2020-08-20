<template>

	<div>
        <template v-if="goodsList.length > 0">
            <estimates-goods-items-component
                :items="goodsList"
                :settings="settings"
                :stocks="stocks"
            ></estimates-goods-items-component>
        </template>

        <template v-if="servicesList.length > 0">
            <estimates-services-items-component :items="servicesList"></estimates-services-items-component>
        </template>

        <div v-if="estimateAmount > 0">Общая стоимость: {{ estimateAmount | decimalPlaces | decimalLevel }}</div>
        <div v-if="estimateTotalPoints > 0">Сумма поинтов: {{ estimateTotalPoints | onlyInteger | decimalLevel }}</div>
        <div v-if="estimateItemsDiscount > 0">Сумма скидок по позициям: {{ estimateItemsDiscount | decimalPlaces | decimalLevel }}</div>
        <div v-if="estimateDiscountCurrency > 0">{{ estimateDiscount.name}}<span v-if="estimateDiscount.mode == 1"> {{ estimateDiscount.percent | decimalPlaces | decimalLevel }}%</span>: {{ estimateDiscountCurrency | decimalPlaces | decimalLevel }}</div>
        <div v-if="estimateTotal > 0">Итого к оплате: {{ estimateTotal | decimalPlaces | decimalLevel }}</div>

        <div class="grid-x">
            <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">
                <register-button-component></register-button-component>
            </div>

<!--            <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">-->
<!--                <production-button-component></estimate-production-button-component>-->
<!--            </div>-->

            <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">
                <sale-button-component></sale-button-component>
            </div>

            <div class="small-12 medium-2 small-text-center medium-text-left cell tabs-button tabs-margin-top">
                <print-button-component></print-button-component>
            </div>

        </div>

	</div>

</template>

<script>
    export default {
		components: {
			'estimates-goods-items-component': require('./goods/EstimatesGoodsItemsComponent'),
            'estimates-services-items-component': require('./services/EstimatesServicesItemsComponent'),
            'register-button-component': require('./buttons/RegisterButtonComponent'),
            'sale-button-component': require('./buttons/SaleButtonComponent'),
            'print-button-component': require('./buttons/PrintButtonComponent'),
		},
        props: {
            estimate: Object,
            settings: Array,
            stocks: Array,
        },
        created: function () {
            this.$store.commit('SET_ESTIMATE', this.estimate);
            this.$store.commit('SET_GOODS_ITEMS', this.estimate.goods_items);
            this.$store.commit('SET_SERVICES_ITEMS', this.estimate.services_items);
            this.$store.commit('SET_DISCOUNTS', this.estimate.discounts);
        },
		data() {
			return {
				//
				id: null,

				cost: null,

				itemGoods: null,
				itemGoodsName: null,
				itemGoodsIndex: null,

                itemServices: null,
                itemServicesName: null,
                itemServicesIndex: null,
			}
		},
		computed: {
		    // Товары
			goodsList() {
				return this.$store.state.estimate.goodsItems;
			},

            // Услуги
            servicesList() {
                return this.$store.state.estimate.servicesItems;
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
		methods: {
            openModalServices(item, index) {
                this.itemServicesIndex = index;
                this.itemServices = item;
                this.itemServicesName = item.product.process.name;
            },
			// changeCost: function(value) {
			// 	this.cost = value;
			// },
			// checkChange: function () {
			// 	this.change = false;
			// },
			// setId: function (id) {
			// 	this.id = id;
			// 	if (id != null) {
			// 		this.categoriesItems.filter(item => {
			// 			if (item.id === id && item.entity_id === this.entity_id) {
			//
			// 				// Смотрим в чем принимать
			// 				if (item.article.package_status === 1) {
			// 					this.itemUnit = item.article.package_abbreviation;
			// 				} else {
			// 					this.itemUnit = item.article.unit.abbreviation;
			// 				}
			//
			// 				// Смотрим производителя
			// 				if (item.article.manufacturer_id != null) {
			// 					this.itemManufacturer = item.article.manufacturer_id;
			// 				}
			// 			}
			// 		});
			// 	} else {
			// 		this.itemUnit = null;
			// 		this.itemManufacturer = null;
			// 	}
			// },


            updateServicesItem: function(item) {
                this.$store.commit('UPDATE_SERVICES_ITEM', item);
            },

            deleteServicesItem() {
                this.$store.dispatch('REMOVE_SERVICES_ITEM_FROM_ESTIMATE', this.itemServices.id);
                $('#delete-estimates_services_item').foundation('close');
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
