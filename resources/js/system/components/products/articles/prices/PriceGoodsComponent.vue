<template>
    <tr
        class="item"
        :id="'prices_goods-' + priceGoods.id"
    >

        <td>{{ priceGoods.catalog.name }}</td>
        <td>
            <template v-if="priceGoods.catalogs_item.parent">
                <template v-if="priceGoods.catalogs_item.parent.parent">
                    {{ priceGoods.catalogs_item.parent.parent.name }} /
                </template>
                {{ priceGoods.catalogs_item.parent.name }} /
            </template>
            {{ priceGoods.catalogs_item.name }}
        </td>
        <td v-if="priceGoods.filial">{{ priceGoods.filial.name }}</td>
        <td v-else>Общая</td>
        <td class="price">
<!--            <input-->
<!--                type="number"-->
<!--                v-model="price"-->
<!--                v-if="change"-->
<!--                @keydown.enter.prevent="updateItem()"-->
<!--                v-focus-->
<!--                @focusout="change = false"-->
<!--            >-->
            <digit-component
                v-if="change"
                :value="priceGoods.price"
                @change="changePrice"
                @enter="updateItem"
                @blur="change = false"
            ></digit-component>
            <span
                v-else
                @click="change = true"
            >{{ priceGoods.price | decimalPlaces | decimalLevel }}</span> {{ priceGoods.currency.abbreviation }}
        </td>
        <td>{{ discount | decimalPlaces | decimalLevel }}%</td>
        <td>{{ priceGoods.total | decimalPlaces | decimalLevel }} {{ priceGoods.currency.abbreviation }}</td>

        <td>
            <div
                class="black sprite"
                :class="[{'icon-display-show' : priceGoods.display == 1}, {'icon-display-hide' : priceGoods.display == 0}]"
                data-open="item-display">

            </div>
        </td>
        <td class="td-delete">
<!--                        @can('delete', $cur_price_goods)-->
            <a
                class="icon-delete sprite"
                data-open="delete-price_goods"
                @click="openModalRemoveItem"
            ></a>
<!--                        @endcan-->
        </td>
    </tr>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../../inputs/DigitComponent')
        },
        props: {
            priceGoods: Object,
        },
        data() {
            return {
                price: parseFloat(this.priceGoods.price),
                change: false
            }
        },
        computed: {
            discount() {
                return parseFloat((this.priceGoods.price - this.priceGoods.total) / (this.priceGoods.price / 100));
            }
            // price: {
            //     get () {
            //         return parseInt(this.priceGoods.price);
            //     },
            //     set (value) {
            //         this.priceInput = parseInt(value)
            //     }
            // },
        },

        methods: {
            changePrice(value) {

            },
            updateItem(value) {
                this.change = false;

                axios
                    .patch('/admin/catalogs_goods/' + this.priceGoods.catalogs_goods_id + '/prices_goods/' + this.priceGoods.id, {
                        price: parseFloat(value),
                    })
                    .then(response => {
                        this.$emit('update', response.data);
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            openModalRemoveItem() {
                this.$emit('open-modal-remove', this.priceGoods);
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
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        },
    }
</script>
