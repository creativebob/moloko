<template>
    <div class="grid-x grid-padding-x">

        <div class="shrink cell catalog-bar">
            <div class="grid-x grid-padding-x">

                <div class="small-12 cell search-in-catalog-panel">
                    <label class="label-icon">
                        <input type="text" name="search" placeholder="Поиск" maxlength="25" autocomplete="off">
                        <div class="sprite-input-left icon-search"></div>
                        <span class="form-error"></span>
                    </label>
                </div>

                <div class="small-12 cell search-in-catalog-panel">

                    <ul
                            class="vertical menu selecter-catalog-item"
                            v-drilldown
                            data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'
                    >

                        <childrens-component
                                v-for="catalogGoodsItem in catalogGoodsItemsList"
                                :item="catalogGoodsItem"
                                :key="catalogGoodsItem.id"
                                @get="getPrices"
                        ></childrens-component>

                    </ul>

                </div>
            </div>
        </div>

        <div class="auto cell">

            <div class="grid-x grid-padding-x">
                <div class="small-10 cell view-settings-panel">
                    <div
                        class="one-icon-16 icon-view-list icon-button"
                        :class="[{active: view == 'view-list'}]"
                        @click="view = 'view-list'"
                    ></div>
                    <div
                        class="one-icon-16 icon-view-block icon-button"
                        :class="[{active: view == 'view-block'}]"
                        @click="view = 'view-block'"
                    ></div>
                    <div
                        class="one-icon-16 icon-view-card icon-button"
                        :class="[{active: view == 'view-card'}]"
                        @click="view = 'view-card'"
                    ></div>
                </div>
                <div class="small-2 cell global-settings-panel">
                    <div
                        class="one-icon-16 icon-view-setting icon-button"
                        data-open="modal-catalogs-goods">
                    </div>
                </div>
            </div>

            <div class="grid-x" id="block-prices_goods">
                <ul
                        class="small-12 cell products-list"
                        :class="view"
                        v-show="listPrices.length > 0"
                >
                    <template v-for="price in listPrices">
                        <li v-bind:class="{ priority: price.is_priority, hit: price.is_hit, new: price.is_new }">
                            <a @click="addPriceToEstimate(price)">

                                <!-- Отрисовываем ссылку на фото только в режиме отображения товаров "Карточкой", дабы не грузить браузер -->
                                <div v-if="view == 'view-card'" class="prise-photo">
                                    <img :src="getPhotoPath(price, 'small')">
                                </div>

                                <div class="grid-x">
                                    <div class="cell main-block">
                                        <div class="grid-x">
                                            <div class="cell auto price-name">
                                                <h4>
                                                    <span class="items-product-name">{{ price.goods.article.name }}</span>
<!--                                                    <span class="items-product-manufacturer"> ({{ $cur_prices_goods->goods->article->manufacturer->name ?? '' }})</span>-->
                                                </h4>
                                            </div>

                                            <div class="cell shrink wrap-product-price">
                                                <span
                                                    class="items-product-price"
                                                    :class="[{'with-discount' : price.price != price.total_catalogs_item_discount }]"
                                                >{{ price.total_catalogs_item_discount | roundToTwo | level }}</span>
                                                <span v-if="price.points" class="points">({{ price.points | roundToTwo | level }})</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="cell extra-block">
                                        <div class="grid-x extra-info">
                                            <div class="cell auto">
                                                <span v-if="((price.price - price.total_catalogs_item_discount) * 100 / price.price)>0" class="price-discount-extra">{{ (price.price - price.total_catalogs_item_discount) * 100 / price.price }}%</span>
                                                <span v-if="price.is_hit" class="price-hit">Hit</span>
                                                <span v-if="price.is_new" class="price-new">New</span>
                                            </div>
                                            <div class="cell shrink counter-price-goods">
                                                3
                                            </div>
                                        </div>
                                    </div>

<!--                                                 <div class="cell desc-block">
                                        <p class="items-product-description">{{ price.goods.description }}</p>
                                    </div> -->
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </div>
        </div>

        <div class="reveal rev-small" id="modal-catalogs-goods" data-reveal>
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Настройка каталога товаров</h5>
                </div>
            </div>
            <div class="grid-x align-center modal-content">
                <div class="small-10 cell text-center inputs">

                    <select
                            v-model="changeCatalogId"
                    >
                        <option
                                :value="catalog.id"
                                v-for="catalog in catalogs"
                                :selected="catalogId"
                        >{{ catalog.name}}</option>
                    </select>
                </div>
                <div class="small-10 cell inputs">
                        <div class="grid-x align-left">
                            <div class="cell small-12 medium-4 checkbox">
                                <input type="checkbox" name="show_hit" id="checkbox-show-hit">
                                <label for="checkbox-show-hit"><span>Хиты</span></label>
                            </div>
                             <div class="cell small-12 medium-4 checkbox">
                                <input type="checkbox" name="show_new" id="checkbox-show-new">
                                <label for="checkbox-show-new"><span>Новинки</span></label>
                            </div>
                             <div class="cell small-12 medium-4 checkbox">
                                <input type="checkbox" name="show_out_of_stock" id="checkbox-show-out-of-stock">
                                <label for="checkbox-show-out-of-stock"><span>Нет на складе</span></label>
                            </div>
                            <div class="cell small-12 medium-4 checkbox">
                                <input type="checkbox" name="show_priority" id="checkbox-show-priority">
                                <label for="checkbox-show-priority"><span>Приоритет</span></label>
                            </div>
                             <div class="cell small-12 medium-4 checkbox">
                                <input type="checkbox" name="show_preorder" id="checkbox-show-preorder">
                                <label for="checkbox-show-preorder"><span>Под заказ</span></label>
                            </div>
                        </div>
                </div>
            </div>

            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                            class="button modal-button button-change-catalog_goods"
                            type="submit"
                            @click.prevent="changeCatalog"
                    >Использовать</button>
                </div>
            </div>
            <div data-close class="icon-close-modal sprite close-modal"></div>
        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'childrens-component': require('../common/CatalogsItemsChildrensComponent.vue')
        },
        props: {
            catalogsGoodsData: Object,
            // isPosted: Boolean,
        },
        data() {
            return {
                view: 'view-list',

                catalogId: this.catalogsGoodsData.catalogsGoods[0].id,
                catalogs: this.catalogsGoodsData.catalogsGoods,
                catalogsItems: this.catalogsGoodsData.catalogsGoodsItems,
                prices: this.catalogsGoodsData.catalogsGoodsPrices,
                listPrices: [],
                changeCatalogId: this.catalogsGoodsData.catalogsGoods[0].id
        }
        },
        computed: {
            catalogGoodsItemsList() {
                return this.catalogsItems.filter(item => {
                    return item.catalogs_goods_id === this.catalogId;
                });
            },
        },
        methods: {
            getPrices(id) {
                this.listPrices = this.prices.filter(item => {
                    return item.catalogs_goods_item_id === id;
                });
            },
            changeCatalog() {
                if (this.catalogId !== this.changeCatalogId) {
                    this.catalogId = this.changeCatalogId;
                    this.getPrices(0);
                }

                $('#modal-catalogs-goods').foundation('close');
            },
            addPriceToEstimate(price) {
                this.$store.commit('ADD_GOODS_ITEM_TO_ESTIMATE', price);
            },
            getPhotoPath(price, format) {

                // Умолчание по формату. Плюс защита от ошибок при указании формата
                (format != ('small' || 'medium' || 'large')) ? format = 'medium' : format;
                return '/storage/' + price.company_id + '/media/articles/' + price.goods.article.id + '/img/' + format + '/' + price.goods.article.photo.name;
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
