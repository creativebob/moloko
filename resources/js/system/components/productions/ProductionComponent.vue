<template>

	<table class="table-invoice">
		<thead>
			<tr>
				<th>№</th>
				<th>Тип:</th>
				<th>Наименование позиции:</th>
				<th>Кол-во:</th>
				<th>Ед. изм.:</th>
				<th
					v-if="!isProduced"
				></th>
			</tr>
		</thead>

		<tbody id="table-raws">

			<productions-item-component
					v-for="(item, index) in itemsList"
					:item="item"
					:index="index"
					:key="item.id"
					:is-produced="isProduced"
					@update="updateItem"
					@remove="deleteItem(index)"
			></productions-item-component>

			<tr
				v-if="!isProduced"
				class="tr-add"
			>
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
								:selected="entity.id === selectedEntity"
						>{{ entity.name }}</option>
					</select>
				</td>
				<td>
					<select-categories-component
							:select-categories="selectCategories"
							:select-categories-items="selectCategoriesItems"
							:change="change"
							@set-id="setId"
							@check-change="checkChange"
					></select-categories-component>
				</td>
				<td>
					<input
							v-model="count"
							name="count"
							type="number"
					>
<!--					<input-digit-component name="count" rate="2" :value="count" v-on:countchanged="changeCount"></input-digit-component>-->
				</td>
				<td>{{ itemUnit }}</td>
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
				<td	colspan="3">Итого:</td>
				<td>Позиций: {{ totalItemsCount }}</td>
				<td
					v-if="!isProduced"
				></td>
			</tr>
		</tfoot>
	</table>

</template>

<script>
    export default {
		components: {
			'select-categories-component': require('../common/selects_categories/SelectCategoriesComponent.vue'),
			'productions-item-component': require('./ProductionsItemComponent.vue')
		},
		props: {
			production: Object,
			selectData: Object
		},
		data() {
			return {
				// Сущности
				entities: this.selectData.entities,
				selectedEntity: this.selectData.entities[0].id,
				entity_id: this.selectData.entities[0].id,

				//
				items: this.production.items,
				id: null,
				count: null,

				// Категории для компонента выбора
				categories: this.selectData.categories,
				categoriesItems: this.selectData.items,
				change: false,
				itemUnit: null
			}
		},
		computed: {
			isDisabled() {
				return this.id == null  || (this.count == null || this.count == 0)
			},
			itemsList() {
				return this.items;
			},
			totalItemsCount() {
				return this.items.length;
			},

			// Списки для компонента выбора
			selectCategories() {
				return this.categories.filter(item => {
					return item.entity_id === this.entity_id
				})
			},
			selectCategoriesItems() {
				return this.categoriesItems.filter(item => {
					return item.entity_id === this.entity_id
				})
			},
			isProduced() {
				return this.production.is_produced === 1;
			},
		},

		methods: {
			changeCount: function(value) {
				this.count = value;
			},
			changeEntity: function() {
				this.change = true;

				let count = 0;
				this.categories.filter(item => {
					if (item.entity_id === this.entity_id) {
						count++
					}
				});

				if (count === 0) {
					axios
						.post('/admin/productions/categories', {
							entity_id: this.entity_id,
						})
						.then(response => {
							this.categories = this.categories.concat(response.data.categories);
							this.categoriesItems = this.categoriesItems.concat(response.data.items);
						})
						.catch(error => {
							console.log(error);
						})
				}

			},
			checkChange: function () {
				this.change = false;
			},
			setId: function (id) {
				this.id = id;
				if (id != null) {
					this.categoriesItems.filter(item => {
						if (item.id === id && item.entity_id === this.entity_id) {
							this.itemUnit = item.article.unit.abbreviation;
						}
					});
				} else {
					this.itemUnit = null;
				}
			},

			addItem: function() {
				if (!this.isDisabled) {
					this.disabledButton = true;
					axios
						.post('/admin/productions_items', {
							production_id: this.production.id,
							cmv_id: this.id,
							entity_id: this.entity_id,
							count: this.count,
						})
						.then(response => {
								this.items.push(response.data)
							},
								this.id = null,
								this.count = null,
								this.change = true
						)
						.catch(error => {
							console.log(error)
						});
				}
			},
			updateItem: function(item, index) {
				Vue.set(this.items, index, item);
			},

			deleteItem: function(index) {
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
