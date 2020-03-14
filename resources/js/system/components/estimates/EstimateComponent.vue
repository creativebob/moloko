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

        <div v-if="estimateAmount > 0"
        >Общая стоимость: {{ estimateAmount | roundToTwo | level }}</div>

	</div>

</template>

<script>
    export default {
		components: {
			'estimates-goods-items-component': require('./goods/EstimatesGoodsItemsComponent.vue'),
            'estimates-services-items-component': require('./services/EstimatesServicesItemsComponent.vue')
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
			goodsList() {
				return this.$store.state.estimate.goodsItems;
			},
            servicesList() {
                return this.$store.state.estimate.servicesItems;
            },
            estimateAmount() {
                return this.$store.getters.estimateAmount;
            },
            estimateTotal() {
                return this.$store.getters.estimateTotal;
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
			roundToTwo: function (value) {
				return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
			},

			// Создает разделители разрядов в строке с числами
			level: function (value) {
				return Number(value).toLocaleString();
			},
		},

	}
</script>
