<template>
    <table class="table-cart">
        <tbody>
        <cart-item-component
            v-for="(item, index) in items"
            :item="item"
            :key="item.id"
            :index="index"
        ></cart-item-component>
        </tbody>
        <tfoot v-if="! items.length">
            <tr class="tr-empty-cart">
                <td colspan="6">В корзине ничего нет</td>
            </tr>
        </tfoot>
        <tfoot
            v-if="cartTotal > 0"
        >
        <tr>
            <td class="number-item-cart-tfoot"></td>
            <td class="goods-name-total">ИТОГО</td>
            <td class="goods-price-item-cart-tfoot"></td>
            <td></td>
            <td class="total-count">
                <span>{{ cartCount | level }}</span>
            </td>
            <td class="goods-price-total"><span :id="total_price">{{ cartTotal | onlyInteger | level }}</span> ₽
            </td>
            <td class="goods-del-item-cart-tfoot"></td>
        </tr>
        <template v-if="discount">
            <tr>
                <td></td>
                <td class="goods-discount-total" colspan="3">{{ discount.name }} ({{ discount.percent }}%)</td>
                <td></td>
                <td><span>{{ cartTotal / 100 * discount.percent | level }}</span> руб.</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="goods-name-total" colspan="2">ИТОГО со скидкой</td>
                <td class="total-count">
                </td>
                <td></td>
                <td class="goods-price-total">
                    <span>{{ cartTotal - (cartTotal / 100 * discount.percent) | onlyInteger | level }}</span> руб.
                </td>
            </tr>
        </template>
        </tfoot>
    </table>
</template>

<script>
export default {
    components: {
        'cart-item-component': require('./CartItemComponent'),
    },
    props: {
        discount: {
            type: Object,
            default: null
        },
    },
    computed: {
        items() {
            return this.$store.state.goodsItems;
        },
        cartTotal() {
            return this.$store.getters.CART_TOTAL_CATALOGS_ITEM_DISCOUNT;
        },
        cartCount() {
            return this.$store.getters.CART_COUNT;
        }
    },
    filters: {
        level: function (value) {
            return value.toLocaleString();
        },
        onlyInteger: function (value) {
            return Math.floor(value);
        },
    }
}

</script>

