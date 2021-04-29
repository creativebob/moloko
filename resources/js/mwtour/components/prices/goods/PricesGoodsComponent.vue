<template>
    <div class="grid-x grid-padding-x">

        <div class="cell small-12">
            <search-component
                @change="changeHide"
            ></search-component>
        </div>

        <div
            v-show="! hide"
            class="cell small-12"
        >
            <table class="unstriped table-prices_goods">
                <tbody>
                    <price-goods-component
                        v-for="price in pricesGoods"
                        :price="price"
                        :key="price.id"
                    ></price-goods-component>
                </tbody>
            </table>
        </div>

    </div>
</template>

<script>
    export default {
        components: {
            'price-goods-component': require('./PriceGoodsComponent.vue'),
            'search-component': require('./SearchComponent.vue'),
        },
        props: {
            catalogsItemId: Number
        },
        data() {
            return {
                pricesGoods: [],
                hide: false,
            }
        },
        created() {
            axios
                .get('/api/v1/get_prices_from_catalogs_goods_item/' + this.catalogsItemId)
                .then(response => {
                    this.pricesGoods = response.data;
                })
                .catch(error => {
                    console.log(error);
                    alert('Произошла ошибка, перезагрузите страницу!')
                });
        },
        methods: {
            changeHide(value) {
                this.hide = value;
            }
        }
    }
</script>
