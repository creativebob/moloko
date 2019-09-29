<template>

	<table class="table-consignments">
		<thead>
			<tr>
				<th>№</th>
				<th>Тип:</th>
				<th>Наименование позиции:</th>
				<th>Кол-во:</th>
				<th>Цена:</th>
				<th>Сумма:</th>
	<!--			<th>% НДС:</th>-->
	<!--			<th>НДС:</th>-->
	<!--			<th>Всего:</th>-->
				<th></th>
			</tr>
		</thead>

		<tbody id="table-raws">

			<consignments-item-component
					v-for="(item, index) in itemsList"
					:item="item"
					:index="index"
					:key="item.id"
					:upd-item="updItem"
					:del-item="delItem"
			></consignments-item-component>

			<tr class="tr-add">
				<td>{{ items.length + 1}}</td>
				<td>
					<select
							v-model="entity_id"
							name="entity_id"
							@change="changeEntity"
					>
						<option
								v-for="entity in entities"
								:value="entity.id"
								:selected="entity.id == selectedEntity"
						>{{ entity.name }}</option>
					</select>
				</td>
				<td>
					<select-categories-component :select-categories="selectCategories" :select-categories-items="selectCategoriesItems" :hide="hideCategories" :get-id="changeCount"></select-categories-component>
				</td>
				<td>
					<input-digit-component name="count" rate="2" :value="checkCount" v-on:countchanged="changeCount"></input-digit-component>
				</td>
				<td>
					<input-digit-component name="price" :value="checkPrice" v-on:countchanged="changePrice"></input-digit-component>
				</td>
				<td>
					<span>{{ totalItemSum | roundToTwo }}</span>
				</td>
	<!--			<td>-->
	<!--				<select v-model="vat_rate" name="vat_rate">-->
	<!--					<option value="0">Без НДС</option>-->
	<!--					<option value="10">10</option>-->
	<!--					<option value="20">20</option>-->
	<!--				</select>-->
	<!--			</td>-->
	<!--			<td><span> {{ price * count_item * vat_rate / 100 | roundToTwo }} </span></td>-->
	<!--			<td><span> {{ (count_item * price) + (count_item * price * vat_rate / 100) | roundToTwo }} </span></td>-->
				<td>
					<a
							@click="addItem"
							class="button tiny"
							:disabled="isDisabled"
					>Добавить</a>
				</td>
			</tr>

		</tbody>

		<tfoot>
			<tr>
				<td colspan="4">Итого:</td>
				<td>Позиций: {{ totalItemsCount }}</td>
				<td>Сумма: {{ totalItemsPrice }}</td>
				<td></td>
			</tr>
		</tfoot>
	</table>

</template>

<script>
    export default {

		components: {
			'select-categories-component': require('./SelectCategoriesComponent.vue'),
			'consignments-item-component': require('./ConsignmentsItemComponent.vue')
		},

		props: {
			consignment: Object,
			selectData: Object
		},

		data() {
			return {
				// Сущности
				entities: this.selectData.entities,
				selectedEntity: this.selectData.entities[0].id,
				entity_id: this.selectData.entities[0].id,

				//
				items: this.consignment.items,
				id: null,
				count: 0,
				price: 0,
				disabledИгеещт: true,

				// Категории лоя компонента выбора
				categories: this.selectData.categories,
				categoriesItems: this.selectData.items,
				hideCategories: true,
			}
		},

		computed: {
			totalItemSum() {
				return this.count * this.price;
			},
			isDisabled() {
				if (this.id == null) {
					this.disabledИгеещт = true;
				} else {
					this.disabledИгеещт = false;
				}
				return this.disabledИгеещт
			},
			itemsList() {
				return this.items;
			},
			totalItemsCount() {
				return this.items.length;
			},
			totalItemsPrice() {
				let price = 0;
				this.items.forEach(function(item) {
					return price += item.total
				});
				return price;
			},

			checkCount() {
				if (this.count == '') {
					this.count = 0;
				}
				return this.count
			},
			checkPrice() {
				if (this.price == '') {
					this.price = 0;
				}
				return this.price
			},

			// Списки для компонента выбора
			selectCategories() {
				return this.categories.filter(item => {
					return item.entity_id == this.entity_id
				})
			},
			selectCategoriesItems() {
				return this.categoriesItems.filter(item => {
					return item.entity_id == this.entity_id
				})
			},
		},

		methods: {
			changeCount: function(value) {
				this.count = value;
			},
			changePrice: function(value) {
				this.price = value;
			},
			changeEntity: function() {

				this.hideCategories = true

				let count = 0;
				this.categories.filter(item => {
					if (item.entity_id == this.entity_id) {
						count++
					}
				})

				if (count == 0) {
					axios
						.post('/admin/consignments/categories', {
							entity_id: this.entity_id,
						})
						.then(response => {
							this.categories = this.categories.concat(response.data.categories);
							this.categoriesItems = this.categoriesItems.concat(response.data.items);
						})
						.catch(error => {
							console.log(error)
						})
				}

			},
			checkHide: function () {
				this.hideCategories = false;
			},
			setId: function (id) {
				this.id = id;
			},

			addItem: function() {
				this.disabledИгеещт = true
				axios
					.post('/admin/consignments_items', {
						consignment_id: this.consignment.id,
						cmv_id: this.id,
						entity_id: this.entity_id,
						count: this.count,
						price: this.price
					})
					.then(response => {
						this.items.push(response.data)

					},
					this.reset()
					)
					.catch(error => {
						console.log(error)
					})
			},
			reset: function () {
				this.id = null
				this.count = 0
				this.price = 0
			},

			updItem: function(item, index) {
				Vue.set(this.items, index, item);
			},

			delItem: function(index) {
				this.items.splice(index, 1);
			}
		},

		filters: {
			roundToTwo: function (value) {
				return Math.trunc(parseFloat(value.toFixed(2)) * 100) / 100;
			}
		}

	}
</script>
