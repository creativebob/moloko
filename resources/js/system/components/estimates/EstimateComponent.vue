<template>

	<div>

		<table class="table-estimate lead-estimate" id="table-estimate">
			<thead>
			<tr>
				<th>Наименование</th>
				<th>Склад</th>
				<th>Цена</th>
				<th>Кол-во</th>
				<!--                                        <th>Себестоимость</th>
															<th>ДопРасх</th>
															<th>Наценка</th> -->
				<th class="th-amount">Сумма</th>
				<th class="th-delete"></th>
				<th class="th-action"><span class="button-to-reserve button-reserve-all" title="Зарезервировать все!"></span></th>
			</tr>
			</thead>

			<tbody id="section-goods" v-if="goodsList.length > 0">

				<estimates-goods-item-component
						v-for="(item, index) in goodsList"
						:item="item"
						:index="index"
						:key="item.id"
						:is-saled="isSaled"
						@open-modal-remove="openModalGoods(item, index)"
						@update="updateItem"
				></estimates-goods-item-component>

			</tbody>

			<tbody id="section-services">

			</tbody>

			<tfoot>
				<tr>
					<td colspan="4" class="text-right">Итого:</td>
					<td>{{ totalItemsAmount | roundToTwo | level }}</td>
					<td colspan="2"></td>
				</tr>
				<tr>
					<td colspan="4" class="text-right">Итого со скидкой ({{ discountPercent }}%):</td>
					<td>{{ totalItemsAmountWithDiscount | roundToTwo | level }}</td>
					<td colspan="2"></td>
				</tr>
			</tfoot>
		</table>

		<div class="reveal rev-small" id="delete-estimates_item" data-reveal>
			<div class="grid-x">
				<div class="small-12 cell modal-title">
					<h5>Удаление</h5>
				</div>
			</div>
			<div class="grid-x align-center modal-content ">
				<div class="small-10 cell text-center">
					<p>Удаляем "{{ itemGoodsName }}", вы уверены?</p>
				</div>
			</div>
			<div class="grid-x align-center grid-padding-x">
				<div class="small-6 medium-4 cell">
					<button
							@click.prevent="deleteGoodsItem"
							data-close
							class="button modal-button"
							type="submit"
					>Удалить</button>
				</div>
				<div class="small-6 medium-4 cell">
					<button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
				</div>
			</div>
		</div>

	</div>

</template>

<script>
    export default {
		components: {
			'estimates-goods-item-component': require('./EstimatesGoodsItemComponent.vue')
		},
		data() {
			return {
				//
				id: null,
				count: null,
				cost: null,
				discountPercent: 10,

				itemGoods: null,
				itemGoodsName: null,
				itemGoodsIndex: null,

				isSaled: this.$store.state.estimate.estimate.is_saled === 1,
			}
		},
		computed: {
			estimate() {
				return this.$store.state.estimate.estimate;
			},
			goodsList() {
				return this.$store.state.estimate.goodsItems;
			},
			totalItemsAmount() {
				return this.$store.getters.estimateAmount;
			},

			totalItemsAmountWithDiscount() {
				return this.$store.getters.estimateTotal;
			},
			showButtonReserved() {
				return this.estimate.is_reserved === 0;
			}


		},

		methods: {
			changeCount: function(value) {
				this.count = value;
			},
			openModalGoods(item, index) {
				this.itemGoodsIndex = index;
				this.itemGoods = item;
				this.itemGoodsName = item.product.article.name;
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

			updateItem: function(item) {
				this.$store.commit('UPDATE_GOODS_ITEM', item);
			},
			deleteGoodsItem() {
				this.$store.dispatch('REMOVE_GOODS_ITEM_FROM_ESTIMATE', this.itemGoods.id);
				$('#delete-estimates_item').foundation('close');
			}
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
