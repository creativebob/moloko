<template>
	<td
		class="td-price"
		@click="checkChange"
	>
		<span
			v-if="!isChange"
		>{{ price | roundToTwo | level }}</span>

		<input
			v-else
			v-model="curPrice"
		>

	</td>

</template>

<script>
	export default {
		props: {
			price: Number
		},
		data(){
			return {
				curPrice: Number(this.price),
				change: false
			}
		},
		computed: {
			isChange() {
				return this.change
			}
		},
		methods: {
			checkChange() {
				this.change = !this.change
			},
		},
		directives: {
			focus: {
				inserted: function (el) {
					el.focus()
				}
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