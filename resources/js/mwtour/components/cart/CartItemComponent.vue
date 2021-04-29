<template>
    <tr
        class="tr-goods"
        :class = "{ deletion: deletion }"
    >
        <td class="number-item-cart"></td>
        <td class="td-img-cart">
            <img
                :src="photoPath"
                class="img-cart"
            >
        </td>
        <td class="goods-name-item-cart">{{ item.goods.article.name }}</td>
        <td class="goods-price-item-cart">
            <span>{{ item.total_catalogs_item_discount| onlyInteger | level }}</span>&nbsp;<span class="currency_symbol">{{ item.currency.symbol }}</span>
        </td>
        <td class="plus-minus">
            <span v-if="item.hasOwnProperty('oldRest')">Внимание, недостаточно товара на складе! Доступно: {{ item.rest }}</span>
            <count-component
                :value="count"
                @add="addCount"
                @deduct="deductCount"
                @change="changeCount"
            >
            </count-component>
        </td>
        <td class="goods-total-item-cart">
            <span v-if="item.hasOwnProperty('oldPrice')">Внимание, изменилась цена! Старая: {{ item.oldPrice }} <span class="currency_symbol">{{ item.currency.symbol }}</span>, новая: </span>
            <span>{{ item.total_catalogs_item_discount | level }}</span>&nbsp;<span class="currency_symbol">{{ item.currency.symbol }}</span>
        </td>
        <td class="goods-del-item-cart">
            <span
                class="delete"
                @click="remove"
            ></span>
        </td>
    </tr>
</template>

<script>
export default {
    components: {
        'count-component': require('../common/CountComponent'),
    },
    props: {
        item: Object,
        index: Number,
    },
    data() {
        return {
            count: this.item.quantity,
            deletion: false,
        }
    },
    computed: {
        photoPath() {
            if (this.item.goods.article.photo) {
                return '/storage/' + this.item.goods.article.company_id + '/media/articles/' + this.item.goods.article.id + '/img/small/' + this.item.goods.article.photo.name;
            } else {
                return '';
            }
        }
    },
    watch: {
        count (value) {
            if (value == 0) {
                this.deletion = true;
                this.interval = setTimeout(() => {
                    this.$store.commit('REMOVE_FROM_CART', this.index);
                }, 2000);

            } else {
                clearTimeout(this.interval);
                this.deletion = false;
            }

            let data = {
                id: this.item.id,
                count: value,
            };
            this.$store.commit('CHANGE_CART', data);
        }
    },
    methods: {
        addCount() {
            this.count++;
        },
        deductCount() {
            this.count--;
        },
        changeCount(value) {
            this.count = value;
        },
        remove() {
            this.$store.commit('REMOVE_FROM_CART', this.index);
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

