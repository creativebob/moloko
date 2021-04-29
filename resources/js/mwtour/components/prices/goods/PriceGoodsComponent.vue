<template>
    <tr class="shop-item">
        <td>
            <picture v-if="photoPathSmall">
                <img
                    :src="photoPathSmall"
                    :alt="price.goods.article.name"
                    width="150"
                    height="99"
                >
            </picture>
        </td>
        <td>
            <h3 class="goods-name">{{ price.goods.article.name }}</h3>
            <ul class="menu vertical goods-prop">
                <li class="manufacturer-name">Производитель: <span class="bold">{{ price.goods.article.manufacturer.company.name }}</span></li>
                <li class="article-name">Артикул: <span class="code">{{ price.goods.article.external }}</span></li>
            </ul>
        </td>
        <td>
            <span class="bold">{{ price.price | onlyInteger | level }} {{ price.currency.abbreviation }}</span>
        </td>
        <td class="plus-minus">
            <count-component
                :value="countInCart"
                @add="addToCart"
                @deduct="deductToCart"
                @change="changeCart"
            ></count-component>
        </td>
    </tr>
</template>

<script>
    export default {
        components: {
            'count-component': require('../../common/CountComponent.vue'),
        },
        props: {
            price: Object,
        },
        data() {
            return {
                item: {
                    id: this.price.id,
                    price: parseFloat(this.price.price),
                    total_catalogs_item_discount_unit: parseFloat(this.price.total_catalogs_item_discount),
                    total_catalogs_item_discount: parseFloat(this.price.total_catalogs_item_discount),
                    total_unit: parseFloat(this.price.total),
                    total: parseFloat(this.price.total),
                    count: 1,
                    goods: this.price.goods,
                    currency: this.price.currency,

                    rest: parseInt(this.price.goods.rest),
                    is_check_stock: this.price.catalog.is_check_stock,
                },
                // photoPathMedium: '/storage/' + this.price.company_id + '/media/articles/' + this.price.goods.article.id + '/img/medium/' + this.price.goods.article.photo.name,
                // photoPathLarge: '/storage/' + this.price.company_id + '/media/articles/' + this.price.goods.article.id + '/img/large/' + this.price.goods.article.photo.name,
            }
        },
        computed: {
            photoPathSmall(){
                if (this.price.goods.article.photo) {
                    return '/storage/' + this.price.company_id + '/media/articles/' + this.price.goods.article.id + '/img/small/' + this.price.goods.article.photo.name;
                } else {
                    return false;
                }
            },
            countInCart() {
                return this.$store.getters.COUNT_IN_CART(this.price.id);
            },
        },
        methods: {
            addToCart() {
                this.$store.commit('ADD_TO_CART', this.item);
            },
            deductToCart() {
                this.$store.commit('DEDUCT_TO_CART', this.item);
            },
            changeCart(count) {
                const data = {
                    id: this.item.id,
                    count: parseFloat(count)
                };
                this.$store.commit('CHANGE_CART', data);
            },
        },

        filters: {
            level: function (value) {
                return value.toLocaleString();
            },
            onlyInteger: function (value) {
                return Math.floor(value);
            },
        },
    }
</script>
