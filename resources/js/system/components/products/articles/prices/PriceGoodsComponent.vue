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
            <input
                type="number"
                v-model="price"
                v-if="change"
                @keydown.enter.prevent="updateItem()"
                v-focus
                @focusout="change = false"
            >
            <span
                v-else
                @click="change = true"
            >{{ priceGoods.price }}</span> {{ priceGoods.currency.abbreviation }}
        </td>

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
        props: {
            priceGoods: Object,
        },
        data() {
            return {
                priceInput: parseInt(this.priceGoods.price),
                change: false
            }
        },
        computed: {
            price: {
                get () {
                    return parseInt(this.priceGoods.price);
                },
                set (value) {
                    this.priceInput = parseInt(value)
                }
            },
        },

        methods: {
            updateItem: function() {
                this.change = false;

                axios
                    .patch('/admin/catalogs_goods/' + this.priceGoods.catalogs_goods_id + '/update_prices_goods', {
                        id: this.priceGoods.id,
                        price: parseInt(this.priceInput),
                    })
                    .then(response => {
                        this.$emit('update', response.data);
                        this.priceInput = parseInt(response.data.price);
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
    }
</script>
