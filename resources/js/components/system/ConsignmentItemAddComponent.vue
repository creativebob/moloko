<template>
	<table class="table-compositions">
		<thead v-pre>
			<tr>
				<th>№</th>
				<th>Наименование позиции:</th>
				<th>Кол-во:</th>
				<th>Цена:</th>
				<th>Сумма:</th>
				<th>% НДС:</th>
				<th>НДС:</th>
				<th>Всего:</th>
				<th></th>
			</tr>
		</thead>

		<tbody id="table-raws">
			<tr>
				<td>1</td>
				<td>
					<div class="wrap-input-table">
						<input v-model="mes" type="text" class="name-field padding-to-placeholder">
					</div>
				</td>
				<td>
					<input-digit-component name="count_item" rate="2" :value="855" v-on:countchanged="changeCount"></input-digit-component>
				</td>
				<td>
					<input-digit-component name="price" v-on:countchanged="changePrice"></input-digit-component>
				</td>
				<td>
					<span>{{ count_item * price | roundtotwo }}</span>
				</td>
				<td>
					<select v-model="vat_rate" name="vat_rate">
						<option value="0">Без НДС</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
				</td>
				<td><span> {{ price * count_item * vat_rate / 100 | roundtotwo }} </span></td>
				<td><span> {{ (count_item * price) + (count_item * price * vat_rate / 100) | roundtotwo }} </span></td>
				<td><a class="button tiny">{{ mes }}</a></td>
			</tr>
			<tr v-for="item in consignment.items">
				<td>{{ item.id }}</td>
				<td>{{ item.smv.aricle.name }}</td>
				<td>{{ item.count }}</td>
				<td>{{ item.price }}</td>
				<td>{{ item.amount }}</td>
				<td>{{ item.vat_rate }}</td>
				<td>{{ item.amount_vat }}</td>
				<td>{{ item.total }}</td>
				<td></td>
			</tr>
		</tbody>
	</table>
</template>

<script>
    export default {
		data() {
			return {
				mes: 'Добавить',
				count_item: 123,
				price: 0,
				count_price: 0,
				vat_rate: 20,
				vat_rate_price: 0,
				total: 0, 
				point_status: false,
				display: false
			}
		},

		props: ['consignment'],

		created: function(){

		},

		methods: {
			changeCount: function(value) {
				this.count_item = value;
			},
			changePrice: function(value) {
				this.price = value;
			}
		},

		filters: {
			roundtotwo: function (value) {
				return Math.trunc(parseFloat(value.toFixed(2)) * 100) / 100;
			}
		}

	}
</script>
