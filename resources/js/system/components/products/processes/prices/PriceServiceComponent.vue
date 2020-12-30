<template>
    <tr
        class="item"
        :id="'prices_goods-' + priceService.id"
    >

        <td>{{ priceService.catalog.name }}</td>
        <td>
            <template v-if="priceService.catalogs_item.parent">
                <template v-if="priceService.catalogs_item.parent.parent">
                    {{ priceService.catalogs_item.parent.parent.name }} /
                </template>
                {{ priceService.catalogs_item.parent.name }} /
            </template>
            {{ priceService.catalogs_item.name }}
        </td>
        <td v-if="priceService.filial">{{ priceService.filial.name }}</td>
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
                :value="priceService.price"
                @input="changePrice"
                @enter="updateItem"
                @blur="change = false"
            ></digit-component>
            <span
                v-else
                @click="change = true"
            >{{ priceService.price | decimalPlaces | decimalLevel }}</span> {{ priceService.currency.abbreviation }}
        </td>
        <td>{{ discount | decimalPlaces | decimalLevel }}%</td>
        <td>{{ priceService.total | decimalPlaces | decimalLevel }} {{ priceService.currency.abbreviation }}</td>

        <td>
            <div
                class="black sprite"
                :class="[{'icon-display-show' : priceService.display == 1}, {'icon-display-hide' : priceService.display == 0}]"
                data-open="item-display">

            </div>
        </td>
        <td class="td-delete">
            <a
                class="icon-delete sprite"
                data-open="delete-price_service"
                @click="openModalRemoveItem"
            ></a>
        </td>
    </tr>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../../inputs/DigitComponent')
        },
        props: {
            priceService: Object,
        },
        data() {
            return {
                price: parseFloat(this.priceService.price),
                change: false
            }
        },
        computed: {
            discount() {
                return parseFloat((this.priceService.price - this.priceService.total) / (this.priceService.price / 100));
            }
            // price: {
            //     get () {
            //         return parseInt(this.priceService.price);
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
                    .patch('/admin/catalogs_services/' + this.priceService.catalogs_service_id + '/prices_services/' + this.priceService.id, {
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
                this.$emit('open-modal-remove', this.priceService);
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
