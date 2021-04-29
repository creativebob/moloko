<template>
    <div class="grid-x" id="wrap-elements-search">
        <div class="cell small-12 medium-6 wrap-search-field">
            <input
                type="search"
                name="search"
                v-model="text"
                @input="debounceSearch"
                class="search"
                placeholder="Введите название или артикул"
            >
        </div>

        <div
            v-if="search"
            class="cell small-12 wrap-search-result"
        >
            <table
                v-if="results.length > 0"
                class="unstriped table-prices_goods"
            >
                <tbody>
                    <price-goods-component
                        v-for="(price, index) in results"
                        :price="price"
                        :index="index"
                        :key="price.id"
                    ></price-goods-component>
                </tbody>
            </table>
            <p
                v-else
            >Ничего не найдено</p>
        </div>
    </div>
</template>

<script>
    import _ from 'lodash'

    export default {
        components: {
            'price-goods-component': require('./PriceGoodsComponent.vue'),
        },
        data() {
            return {
                search: false,
                text: '',
                results: [],
            }
        },
        computed: {
            debounceSearch() {
                let delay = 300;
                return _.debounce(this.check, delay);
            },
        },
        methods: {
            check () {
                // console.log('Ищем введеные данные, затем от результата меняем состояние на поиск или ошибку');
                if (this.text.length >= 2) {
                    axios
                        .get('/prices-goods/search/' + this.text, {
                            // catalog_goods_id: this.catalogsGoodsItem.catalogs_gods_id,
                            // catalog_goods_item_id: this.catalogsGoodsItem.id
                        })
                        .then(response => {
                            this.results = response.data;
                            this.search = true;
                            this.checkSearch();
                            // this.error = (this.results.length == 0)
                        })
                        .catch(error => {
                            console.log(error)
                        });
                } else {
                    this.reset();
                }
            },
            addToCart(price) {
                let item =  {
                    id: price.id,
                    price: parseInt(price.price),
                    count: 1,
                    goods: price.goods
                };
                this.$store.commit('ADD_TO_CART', item);
            },
            deductToCart(price) {
                let item =  {
                    id: price.id,
                    price: parseInt(price.price),
                    count: 1,
                    goods: price.goods
                };
                this.$store.commit('DEDUCT_TO_CART', item);
            },
            reset() {
                // console.log('Изменение в инпуте, обнуляем все кроме имени, и если символов больше 2х начинаем поиск');
                this.search = false;
                this.results = [];

                if (this.text.length > 2) {
                    this.check();
                };

                this.checkSearch();
            },
            countInCart(price) {
                return this.$store.getters.countGoodsCart(price.id);
            },
            checkSearch() {
                this.$emit('change', this.search);
            }

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
