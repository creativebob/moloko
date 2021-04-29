<template>
	<span id="cart">
		<a
            href="/cart"
		>
            <span>{{ totalCount | level }} шт.</span> | <span>{{ totalPrice | level }} руб.</span> <span class="icon-cart"></span>
        </a>
	</span>
</template>

<script>
	export default {
		computed: {
            totalCount() {
                return this.$store.getters.CART_COUNT;
            },
            totalPrice() {
                return this.$store.getters.CART_TOTAL_CATALOGS_ITEM_DISCOUNT;
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
