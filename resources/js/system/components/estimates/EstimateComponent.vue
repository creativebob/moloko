<template>

	<table class="table-estimate" id="table-estimate">
		<thead>
		<tr>
			<th>Наименование</th>
			<th>Кол-во</th>
			<!--                                        <th>Себестоимость</th>
                                                        <th>ДопРасх</th>
                                                        <th>Наценка</th> -->
			<th>Цена</th>
			<th></th>
		</tr>
		</thead>

		<tbody id="section-goods" v-if="goodsList.length > 0">

		<estimates-item-component
				v-for="(item, index) in goodsList"
				:item="item"
				:index="index"
				:key="item.id"
				:is-saled="isSaled"
				@update="updateItem"
				@remove="deleteItem(index)"
		></estimates-item-component>

		</tbody>

		<tbody id="section-services">

		</tbody>

		<tfoot>
		<tr>
			<td colspan="3" class="text-right">Итого:</td>
			<td>{{ totalItemsAmount | roundToTwo | level }}</td>
		</tr>
		<tr>
			<td colspan="3" class="text-right">Итого со скидкой ({{ discountPercent }}%):</td>
			<td>{{ totalItemsAmountWithDiscount | roundToTwo | level }}</td>
		</tr>
		</tfoot>
	</table>

</template>

<script>
    export default {
		components: {
			'estimates-item-component': require('./EstimatesItemComponent.vue')
		},
		props: {
			estimate: Object,
		},
		data() {
			return {
				goodsList: this.estimate.goods_items,

				//
				id: null,
				count: null,
				cost: null,
				discountPercent: 10

			}
		},
		computed: {
			totalItemsAmount() {
				let amount = 0;
				if (this.goodsList.length > 0) {
					this.goodsList.forEach(function(item) {
						return amount += Number(item.price)
					});
				}

				return amount;
			},

			totalItemsAmountWithDiscount() {
				let amount = 0;
				if (this.totalItemsAmount > 0) {
					let discountAmount = (this.totalItemsAmount * this.discountPercent) / 100;

					amount += this.totalItemsAmount - discountAmount;
				}

				return amount;
			},

			isSaled() {
				return this.estimate.is_saled === 1;
			},
		},

		methods: {
			changeCount: function(value) {
				this.count = value;
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

			// addItem: function() {
			// 	if (!this.isDisabled) {
			// 		this.disabledButton = true;
			// 		axios
			// 			.post('/admin/consignments_items', {
			// 				consignment_id: this.consignment.id,
			// 				cmv_id: this.id,
			// 				entity_id: this.entity_id,
			// 				count: this.count,
			// 				cost: this.cost,
			// 				manufacturer_id: this.manufacturer_id,
			// 			})
			// 			.then(response => {
			// 					this.items.push(response.data)
			// 				},
			// 					this.id = null,
			// 					this.count = null,
			// 					this.cost = null,
			// 					this.change = true,
			// 					this.manufacturer_id = null,
			// 					this.itemManufacturer = null,
			//
			// 			)
			// 			.catch(error => {
			// 				console.log(error)
			// 			});
			// 	}
			// },
			// updateItem: function(item, index) {
			// 	Vue.set(this.items, index, item);
			// },
			//
			// deleteItem: function(index) {
			// 	this.items.splice(index, 1);
			// }
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
