<template>
    <div class="grid-x grid-padding-x">

        <div class="shrink cell catalog-bar">
            <div class="grid-x grid-padding-x">

                <div class="small-12 cell search-in-catalog-panel">
                    <label class="label-icon">
                        <input type="text" name="search" placeholder="Поиск" maxlength="25" autocomplete="off">
                        <div class="sprite-input-left icon-search"></div>
                        <span class="form-error">Обязательно нужно логиниться!</span>
                    </label>
                </div>

                <div class="small-12 cell search-in-catalog-panel">

                    <ul
                            class="vertical menu"
                            v-drilldown
                            data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'
                    >
                        <li
                                v-for="item in catalogGoodsItems"
                                class="item-catalog"
                        >
                            <a
                                    @click="getItems(item.id)"
                            >{{ item.name }}</a>

                            <ul
                                    v-if="item.childrens && item.childrens.length"
                                    class="menu vertical nested"
                            >
                                <childrens-component
                                        v-for="children in item.childrens"
                                        :item="children"
                                        :key="children.id"
                                        @get="getItems"
                                ></childrens-component>

                            </ul>
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <div class="auto cell">
            <div class="grid-x grid-padding-x">

                <div class="small-12 cell view-settings-panel">
                    <div class="one-icon-16 icon-view-list icon-button active" id="toggler-view-list"></div>
                    <div class="one-icon-16 icon-view-block icon-button" id="toggler-view-block"></div>
                    <div class="one-icon-16 icon-view-card icon-button" id="toggler-view-card"></div>
                    <div class="one-icon-16 icon-view-setting icon-button" id="open-setting-view" data-open="modal-catalogs-goods"></div>
                </div>

                <div id="block-prices_goods">

                    <ul
                            class="small-12 cell products-list view-list"
                            v-show="listPrices.length > 0"
                    >
                        <li
                                v-for="price in listPrices"
                        >
                            <a
                                    @click="addPriceToEstimate(price.id)"
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
</template>

<script>
    export default {
        components: {
            'childrens-component': require('../common/CatalogsItemsChildrensComponent.vue')
        },
        props: {
            catalogGoods: Object,
            // isPosted: Boolean,
        },
        data() {
            return {
                catalogGoodsItems: this.catalogGoods.catalogGoodsItems,
                // items: this.catalogGoodsItems.items,
                prices: this.catalogGoods.prices,
                listPrices: [],
            }
        },
        computed: {


        },
        methods: {
            getItems(id) {
                this.listPrices = this.prices.filter(item => {
                    return item.catalogs_goods_item_id === id;
                });
            },
        },
        directives: {
            'drilldown': {
                bind: function (el) {
                    new Foundation.Drilldown($(el))
                }
            },
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
