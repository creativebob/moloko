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
					<input @keydown="alarm($event, 'count')" type="text" v-model="count" name="count" class="digit">
				</td>
				<td>
					<input type="text" v-model.number="price" name="price" class="digit">
				</td>
				<td>
					<span>{{ count * price }}</span>
				</td>
				<td>
					<select v-model="vat_rate" name="vat_rate">
						<option value="0">Без НДС</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
				</td>
				<td><span> {{ price * count * vat_rate / 100 }} </span></td>
				<td><span> {{ (count * price) + (count * price * vat_rate / 100) }} </span></td>
				<td><a class="button tiny">{{ mes | capitalize }}</a></td>
			</tr>
		</tbody>
	</table>
</template>

<script>
    export default {
		data() {
			return {
				mes: 'Добавить',
				count: 0,
				price: 0,
				count_price: 0,
				vat_rate: 20,
				vat_rate_price: 0,
				total: 0
			}
		},
		methods: {
			alarm (event, name){
		       if ( !/[0-9\x25\x24\x23\x2e]/.test( event.key ) ) {
		           event.preventDefault();
		       }
			},
			digit(value) {
				exp = /[0-9]/;
				return exp.test(value);
			}
		},
		filters: {
		  capitalize: function (value) {
		    if (!value) return ''
		    value = value.toString()
		    return value.charAt(0).toUpperCase() + value.slice(1)
		  }
		}
	}
</script>
