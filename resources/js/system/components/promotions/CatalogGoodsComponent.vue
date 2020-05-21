<template>
    <div class="grid-x grid-margin-x">
        <div class="small-12 medium-7 cell">
            <div class="grid-x grid-margin-x">

                <div class="small-12 medium-4 cell">
                    <label>Филиал:
                        <select
                            v-model="filialId"
                            @change="reInitMenu"
                        >
                            <option
                                v-for="filial in catalogsGoodsData.filials"
                                :value="filial.id"
                            >{{ filial.name}}</option>
                        </select>
                    </label>
                </div>

            </div>

            <div class="grid-x">
                <div class="small-12 cell">

                    <div class="grid-x grid-margin-x">
                        <div class="small-6 cell">
                            <drilldown-component
                                :actual-catalogs-goods-items="actualCatalogsGoodsItems"
                                @get="getPrices"
                                ref="drilldownComponent"
                            ></drilldown-component>
                        </div>

                        <div class="small-6 cell">
                            <ul
                                class="small-12 cell products-list view-list"
                                v-show="listPrices.length > 0"
                            >
                                <li
                                    v-for="price in listPrices"
                                >
                                    <a
                                        @click="addPrice(price)"
                                    >

                                        <div class="media-object stack-for-small">
                                            <div class="media-object-section items-product-img" >
                                                <div class="thumbnail">
                                                    <!--                                            <img src="{{ getPhotoPath($cur_prices_goods->goods->article, 'small') }}">-->
                                                </div>
                                            </div>

                                            <div class="media-object-section cell">

                                                <div class="grid-x grid-margin-x">
                                                    <div class="cell auto">
                                                        <h4>
                                                            <span class="items-product-name">{{ price.goods.article.name }}</span>
                                                            <!--                                                    <span class="items-product-manufacturer"> ({{ $cur_prices_goods->goods->article->manufacturer->name ?? '' }})</span>-->
                                                        </h4>
                                                    </div>

                                                    <div class="cell shrink wrap-product-price">

                                                        <span class="items-product-price">{{ price.price | roundToTwo | level }}</span>
                                                    </div>
                                                </div>
                                                <p class="items-product-description">{{ price.goods.description }}</p>
                                            </div>
                                        </div>

                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div
            v-if="pricesForSite.length"
            class="small-12 medium-5 cell"
        >
            <fieldset

                class="fieldset-access"
            >
                <legend>{{ catalogName }}</legend>
                <template
                    v-for="filial in catalogsGoodsData.filials"

                >
                    <table
                        v-if="getPricesForSite(filial.id).length"
                        class="hover unstriped"
                    >
                        <caption>{{ filial.name }}</caption>
                        <thead>
                        <tr>
                            <th>№</th>
                            <th>Название</th>
                            <th>Цена</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>

                        <tr
                            v-for="(price, index) in getPricesForSite(filial.id)"
                        >
                            <td>{{ index + 1}}</td>
                            <td>
                                {{ price.goods.article.name }}
                            </td>
                            <td>{{ price.price }}</td>
                            <td class="td-delete">
                                <div
                                    @click="removePrice(price.id)"
                                    class="icon-delete sprite"
                                ></div>
                            </td>

                        </tr>

                        </tbody>
                    </table>
                </template>
            </fieldset>
        </div>

        <tempalate v-if="pricesIds.length">
            <input
                v-for="priceId in pricesIds"
                type="hidden"
                name="prices_goods[]"
                :value="priceId"
            >
        </tempalate>
    </div>
</template>

<script>
    export default {
        components: {
            'drilldown-component': require('./DrilldownComponent.vue')
        },
        props: {
            catalogsGoodsData: Object,
            pricesGoods: Array
        },
        created() {
            if (this.pricesGoods.length) {
                var store = this.$store;
                this.pricesGoods.forEach(function(price) {
                    store.commit('ADD_PRICE', price);
                })
            }
        },
        data() {
            return {
                siteId: this.$store.state.promotion.site.id,
                // actualFilials: [],
                filialId: this.catalogsGoodsData.filials[0].id,
                catalogsGoods: this.catalogsGoodsData.catalogsGoods,
                catalogsGoodsItems: this.catalogsGoodsData.catalogsGoodsItems,
                catalogsGoodsPrices: this.catalogsGoodsData.catalogsGoodsPrices,
                listPrices: [],
                catalogName: '',
            }
        },
        computed: {
            catalogGoodsId() {
                if (this.catalogsGoods.length) {
                    let catalogs = this.catalogsGoods.filter(catalog => {
                        let found = catalog.filials.find(filial => filial.id == this.filialId);
                        if (found) {
                            return catalog;
                        }
                    });

                    if (catalogs.length) {
                        this.catalogName = catalogs[0].name;
                        return catalogs[0].id;
                    } else {
                        return 0;
                    }
                    // let catalogs =  this.catalogsGoods.filter(catalog => {
                    //     let found = catalog.sites.find(site => site.id == this.siteId)
                    //     if (found) {
                    //         return catalog;
                    //     }
                    // });

                    // if (catalogs.length) {
                    //     this.catalogName = catalogs[0].name;
                    //     return catalogs[0].id;
                    // } else {
                    //     return 0;
                    // }
                } else {
                    return 0;
                }
            },
            actualCatalogsGoodsItems() {
                if (this.catalogGoodsId > 0) {
                    return this.catalogsGoodsItems.filter(catalogsItem => catalogsItem.catalogs_goods_id == this.catalogGoodsId);

                } else {
                    return [];
                }
            },
            pricesForSite() {
                return this.$store.state.promotion.prices.filter(price => price.catalogs_goods_id == this.catalogGoodsId);
            },
            pricesIds() {
                var ids = [];
                this.$store.state.promotion.prices.forEach(price => ids.push(price.id));
                return ids;
            }
        },
        methods: {
            getPricesForSite(filialId) {
                return this.$store.state.promotion.prices.filter(price => price.filial_id == filialId && price.catalogs_goods_id == this.catalogGoodsId);
            },
            resetPrices() {
                this.listPrices = [];
            },
            reInitMenu() {
                this.$refs.drilldownComponent.reInit();

                this.resetPrices();
            },
            getPrices(id) {
                this.listPrices = this.catalogsGoodsPrices.filter(price => {
                    return price.catalogs_goods_item_id === id && price.filial_id == this.filialId;
                });
            },
            addPrice(price) {
                let found = this.pricesForSite.find(obj => obj.id == price.id);
                if (!found) {
                    this.$store.commit('ADD_PRICE', price);
                }
            },
            removePrice(priceId) {
                this.$store.commit('REMOVE_PRICE', priceId);
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
