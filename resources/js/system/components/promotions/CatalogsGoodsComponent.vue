<template>
    <div class="grid-x grid-margin-x">
        <div class="small-12 medium-7 cell">
            <div

                class="grid-x grid-margin-x"
            >

                <div class="small-12 medium-4 cell">
                    <label>Сайт:
                        <select
                            :disabled="!actualSites.length"
                            v-model="siteId"
                            @change="resetPrices"
                        >
                            <option
                                v-if="!actualSites.length"
                                value="0"
                            >Выберите сайт</option>
                            <option
                                v-for="(site, index) in actualSites"
                                :value="site.id"
                            >{{ site.name}}</option>
                        </select>
                    </label>
                </div>

                <div class="small-12 medium-4 cell">
                    <label>Филиал:
                        <select
                            :disabled="!actualFilials.length"
                            v-model="filialId"
                            @change="resetPrices"
                        >
                            <option
                                v-if="!actualFilials.length"
                                value="0"
                            >Выберите Филиал</option>
                            <option
                                v-for="filial in actualFilials"
                                :value="filial.id"
                            >{{ filial.name}}</option>
                        </select>
                    </label>
                </div>

                <div class="small-12 medium-4 cell">
                    <label>Каталог:
                        <select
                            v-model="catalogGoodsId"
                            :disabled="!actualCatalogsGoods.length"
                            @change="reInitMenu"
                        >
                            <option
                                v-if="!actualCatalogsGoods.length"
                                value="0"
                            >Выберите каталог</option>
                            <option
                                v-for="catalog in actualCatalogsGoods"
                                :value="catalog.id"

                            >{{ catalog.name}}</option>
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
            v-if="pricesForSites.length"
            class="small-12 medium-5 cell"
        >
            <fieldset
                v-for="item in pricesForSites"
                class="fieldset-access"
            >
                <legend>{{ item.site.name}}</legend>

                <table class="hover unstriped">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Название</th>
                            <th>Цена</th>
                            <th>Филиал</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="(price, index) in item.prices"
                        >
                            <td>{{ index + 1}}</td>
                            <td>{{ price.goods.article.name }}</td>
                            <td>{{ price.price }}</td>
                            <td>{{ price.filial.name }}</td>
                            <td class="td-delete">
                                <div
                                    @click="removePrice(item.siteId, price.id)"
                                    class="icon-delete sprite"
                                ></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'drilldown-component': require('./DrilldownComponent.vue')
        },
        props: {
            catalogsGoodsData: Object,
        },
        data() {
            return {
                siteId: 0,
                // actualFilials: [],
                filialId: 0,
                catalogGoodsId: 0,
                catalogsGoods: this.catalogsGoodsData.catalogsGoods,
                catalogsGoodsItems: this.catalogsGoodsData.catalogsGoodsItems,
                catalogsGoodsPrices: this.catalogsGoodsData.catalogsGoodsPrices,
                // actualCatalogsGoods: [],
                // actualCatalogsGoodsItems: [],
                listPrices: [],
            }
        },
        computed: {
            actualSites() {
                let sites = this.$store.state.promotion.sites;
                if (sites.length) {
                    this.siteId = sites[0].id;
                    this.listPrices = [];
                    return sites;
                } else {
                    this.siteId = 0;
                    return [];
                }
            },
            actualFilials() {
                if (this.siteId > 0) {
                    let found = this.actualSites.find(site => site.id == this.siteId);
                    if (found) {
                        this.filialId = this.actualSites[0].filials[0].id;
                        return found.filials;
                    } else {
                        this.filialId = 0;
                        return [];
                    }

                } else {
                    this.filialId = 0;
                    return [];
                }

            },
            actualCatalogsGoods() {
                if (this.filialId > 0) {
                    if (this.catalogsGoods.length > 0) {
                        let catalogs =  this.catalogsGoods.filter(catalog => {
                            let found = catalog.sites.find(site => site.id == this.siteId)
                            if (found) {
                                return catalog;
                            }
                        })

                        if (catalogs.length) {
                            if (this.catalogGoodsId == 0) {
                                this.catalogGoodsId = catalogs[0].id;
                            }
                            return catalogs;
                        } else {
                            this.catalogGoodsId = 0;
                            this.catalogsGoodsPrices = [];
                            return [];
                        }
                    } else {
                        this.catalogGoodsId = 0;
                        this.catalogsGoodsPrices = [];
                        return [];
                    }

                } else {
                    this.catalogGoodsId = 0;
                    this.listPrices = [];
                    return [];
                }

            },
            actualCatalogsGoodsItems() {
                if (this.catalogGoodsId > 0) {
                    return this.catalogsGoodsItems.filter(catalogsItem => catalogsItem.catalogs_goods_id == this.catalogGoodsId);

                } else {
                    return [];
                }
            },
            pricesForSites() {
                return this.$store.state.promotion.prices;
            }
        },
        methods: {
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
                let site = this.actualSites.find(obj => obj.id == this.siteId);
                let item = {
                    price: price,
                    siteId: site.id,
                    site: site
                };
                let foundSite = this.pricesForSites.find(obj => obj.siteId == item.siteId);
                if (foundSite) {
                    let foundPrice = foundSite.prices.find(obj => obj.id == item.price.id)
                    if (!foundPrice) {
                        this.$store.commit('ADD_PRICE', item);
                    }
                } else {
                    this.$store.commit('ADD_PRICE', item);
                }
            },
            removePrice(siteId, priceId) {
                let item = {
                    priceId: priceId,
                    siteId: siteId,
                };
                this.$store.commit('REMOVE_PRICE', item);
            }
        },

        // directives: {
        //     'drilldown': {
        //         bind: function (el) {
        //             new Foundation.Drilldown($(el))
        //         },
        //         componentUpdated: function(el) {
        //             alert('update')
        //             // new Foundation.Drilldown($(el))
        //         },
        //         // unbind: function (el) {
        //         //     $(el).foundation.destroy()
        //         // }
        //     },
        // },

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
