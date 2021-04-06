<template>
    <div class="grid-x grid-padding-x">

        <div class="shrink cell catalog-bar">
            <div
                v-if="catalogs.length"
                class="grid-x grid-padding-x"
            >

                <search-component
                    :prices="catalogPrices"
                    @add="addPriceToEstimate"
                ></search-component>

                <div class="small-12 cell search-in-catalog-panel">

                    <ul
                        id="drilldown-catalog_goods"
                        class="vertical menu selecter-catalog-item"
                        v-drilldown
                        data-back-button='<li class="js-drilldown-back"><a tabindex="0">Назад</a></li>'
                        data-animate-height="true"
                    >

                        <childrens-component
                            v-for="catalogGoodsItem in catalogGoodsItemsList"
                            :item="catalogGoodsItem"
                            :key="catalogGoodsItem.id"
                            @get="changeCatalogsItem"
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
                        data-open="modal-catalogs_goods">
                    </div>
                </div>
            </div>

            <div class="grid-x" id="block-prices_goods">
                <ul
                    class="cell small-12 products-list"
                    :class="view"
                >
                    <price-goods-component
                        v-for="price in pricesList"
                        :price="price"
                        :view="view"
                        :key="price.id"
                    ></price-goods-component>
                </ul>
            </div>
        </div>

        <div class="reveal rev-small" id="modal-catalogs_goods" data-reveal>
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
                            v-for="catalog in catalogs"
                            :value="catalog.id"
                            :selected="catalogId"
                        >{{ catalog.name }}
                        </option>
                    </select>
                </div>

                <div class="cell small-10 inputs">
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
                    >Использовать
                    </button>
                </div>
            </div>
            <div data-close class="icon-close-modal sprite close-modal"></div>
        </div>
    </div>
</template>

<script>
export default {
    components: {
        'search-component': require('./SearchComponent'),
        'childrens-component': require('../common/CatalogsItemsChildrensComponent'),
        'price-goods-component': require('./PriceGoodsComponent'),
    },
    props: {
        // catalogsGoodsData: Object,
        outlet: Object
        // isPosted: Boolean,
    },
    data() {
        return {
            view: 'view-list',

            catalogId: null,
            catalogsItemId: null,
            catalogs: [],
            catalogsItems: [],
            prices: [],
            listPrices: [],
            changeCatalogId: null
        }
    },
    mounted() {
        this.getCatalogs();
    },
    computed: {
        activeOutlet() {
            return this.$store.state.lead.outlet;
        },
        catalogGoodsItemsList() {
            return this.catalogsItems.filter(item => {
                return item.catalogs_goods_id === this.catalogId;
            });
        },
        pricesList() {
            return this.prices.filter(item => {
                return item.catalogs_goods_item_id === this.catalogsItemId;
            });
        },
        catalogPrices() {
            return this.prices.filter(item => {
                return item.catalogs_goods_id === this.catalogId;
            });
        },
        isDismissed() {
            return this.$store.getters.IS_DISMISSED;
        },
    },
    watch: {
        activeOutlet() {
            this.getCatalogs();
        }
    },
    methods: {
        getCatalogs() {
            if (this.$store.state.lead.outlet.id) {
                axios
                    .post('/admin/catalogs_goods/get_catalogs_for_outlet', {
                        outlet_id: this.$store.state.lead.outlet.id,
                        filial_id: this.$store.state.lead.outlet.filial_id,
                    })
                    .then(response => {
                        if (response.data.success) {
                            this.catalogId = response.data.catalogsGoods[0].id;
                            this.catalogsItemId = response.data.catalogsGoodsItems[0].id;
                            this.catalogs = response.data.catalogsGoods;
                            this.catalogsItems = response.data.catalogsGoodsItems;
                            this.prices = response.data.catalogsGoodsPrices;
                            this.changeCatalogId = response.data.catalogsGoods[0].id;
                        } else {
                            this.catalogId = null;
                            this.catalogsItemId = null;
                            this.catalogs = [];
                            this.catalogsItems = [];
                            this.prices = [];
                            this.changeCatalogId = null;
                        }

                        this.$store.commit('SET_CATALOG_GOODS_ID', this.catalogId);

                        setTimeout(function(){
                            Foundation.reInit($('#drilldown-catalog_goods'));
                        }, 300);
                    })
                    .catch(error => {
                        alert('Ошибка загрузки каталогов товаров, перезагрузите страницу!')
                        console.log(error)
                    });
            }
        },
        changeCatalogsItem(id) {
            this.catalogsItemId = id;
        },
        changeCatalog() {
            if (this.catalogId !== this.changeCatalogId) {
                this.catalogId = this.changeCatalogId;
                const item = this.catalogsItems.find(obj => obj.catalogs_goods_id == this.catalogId);
                this.changeCatalogsItem(item.id);

                this.$store.commit('SET_CATALOG_GOODS_ID', id);
            }

            $('#modal-catalogs_goods').foundation('close');
        },
        addPriceToEstimate(price) {
            if (!this.isDismissed) {
                this.$store.commit('ADD_GOODS_ITEM_TO_ESTIMATE', price);
            }
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
}
</script>
