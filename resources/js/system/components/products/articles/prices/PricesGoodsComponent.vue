<template>
    <div>

        <price-store-component
            :catalogs-data="catalogsData"
            :goods-id="curGoods.id"
            @add="addItem"
            :prices="prices"
            :disabled-filials="disabledFilials"
            ref="childComponent"
        ></price-store-component>

        <table
            v-if="prices.length"
            class="table-compositions"
        >
            <thead>
                <tr>
                    <th>Каталог:</th>
                    <th>Пункт:</th>
                    <th>Филиал:</th>
                    <th>Цена:</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>

            <tbody id="table-prices">

                <price-goods-component
                    v-for="price in prices"
                    :price-goods="price"
                    :key="price.id"
                    @update="updateItem"
                    @open-modal-remove="openModal"
                ></price-goods-component>
            </tbody>
        </table>

        <div class="reveal rev-small" id="delete-price_goods" data-reveal>
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Удаление</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content ">
                <div class="small-10 cell text-center">
                    <p>Удаляем прайс из "<span class="title-price">{{ removeItemName }}</span>", вы уверены?</p>
                </div>
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                        @click.prevent="deleteItem"
                        data-close
                        class="button modal-button"
                    >Удалить</button>
                </div>
                <div class="small-6 medium-4 cell">
                    <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
                </div>
            </div>
            <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
        </div>
    </div>

</template>

<script>
    export default {
        components: {
            'price-store-component': require('./PriceStoreComponent'),
            'price-goods-component': require('./PriceGoodsComponent'),
        },
        props: {
            catalogsData: Object,
            curGoods: Object,
        },
        data() {
            return {
                prices: this.curGoods.prices,
                removeItem: null,
                removeItemName: null,
            }
        },

        computed: {
            disabledFilials() {
                var array = [];
                this.prices.forEach(price => {
                    array.push([
                        price.catalogs_goods_id,
                        price.catalogs_goods_item_id,
                        price.filial_id,
                    ]);
                });
                return array;
            }
        },

        methods: {
            addItem(item) {
                this.prices.push(item);
            },
            updateItem(item) {
                let found = this.prices.find(obj => obj.id == item.id);
                Vue.set(found, 'price', item.price);
            },
            openModal(item) {
                this.removeItem = item;
                this.removeItemName = item.catalog.name;
            },
            deleteItem() {
                var buttons = $('.button');
                buttons.prop('disabled', true);

                axios
                    .post('/admin/catalogs_goods/' + this.removeItem.catalogs_goods_id + '/prices_goods/' + this.removeItem.id + '/archive')
                    .then(response => {
                        if(response.data) {
                            let index = this.prices.findIndex(item => item.id === this.removeItem.id);
                            this.prices.splice(index, 1);
                            buttons.prop('disabled', false);
                            $('#delete-price_goods').foundation('close');
                            this.$refs.childComponent.checkDisabled();
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
        }
    }
</script>
