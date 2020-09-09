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

                        <childrens-component
                                v-for="catalogServicesItem in catalogServicesItemsList"
                                :item="catalogServicesItem"
                                :key="catalogServicesItem.id"
                                @get="getPrices"
                        ></childrens-component>

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
                    <div class="one-icon-16 icon-view-setting icon-button" id="open-setting-view" data-open="modal-catalogs-services"></div>
                </div>

                <div id="block-prices_services">

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
<!--                                            <img src="{{ getPhotoPath($cur_prices_goods->goods->process, 'small') }}">-->
                                        </div>
                                    </div>

                                    <div class="media-object-section cell">

                                        <div class="grid-x grid-margin-x">
                                            <div class="cell auto">
                                                <h4>
                                                    <span class="items-product-name">{{ price.service.process.name }}</span>
<!--                                                    <span class="items-product-manufacturer"> ({{ $cur_prices_goods->goods->process->manufacturer->name ?? '' }})</span>-->
                                                </h4>
                                            </div>

                                            <div class="cell shrink wrap-product-price">

                                                <span class="items-product-price">{{ price.price | roundToTwo | level }}</span>
                                            </div>
                                        </div>
                                        <p class="items-product-description">{{ price.service.description }}</p>
                                    </div>
                                </div>

                            </a>
                        </li>
                     </ul>
                </div>


            </div>
        </div>

        <div class="reveal rev-small" id="modal-catalogs-services" data-reveal>
            <div class="grid-x">
                <div class="small-12 cell modal-title">
                    <h5>Каталоги товаров</h5>
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
            </div>
            <div class="grid-x align-center grid-padding-x">
                <div class="small-6 medium-4 cell">
                    <button
                            class="button modal-button button-change-catalog_services"
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
            catalogsServicesData: Object,
            // isPosted: Boolean,
        },
        data() {
            return {
                catalogId: this.catalogsServicesData.catalogsServices[0].id,
                catalogs: this.catalogsServicesData.catalogsServices,
                catalogsItems: this.catalogsServicesData.catalogsServicesItems,
                prices: this.catalogsServicesData.catalogsServicesPrices,
                listPrices: [],
                changeCatalogId: this.catalogsServicesData.catalogsServices[0].id
        }
        },
        computed: {
            catalogServicesItemsList() {
                let itemsList = this.catalogsItems.filter(item => {
                    return item.catalogs_service_id === this.catalogId;
                });
                return itemsList;
            },
        },
        methods: {
            getPrices(id) {
                this.listPrices = this.prices.filter(item => {
                    return item.catalogs_services_item_id === id;
                });
            },
            changeCatalog() {
                if (this.catalogId !== this.changeCatalogId) {
                    this.catalogId = this.changeCatalogId;
                    this.getPrices(0);
                }

                $('#modal-catalogs-services').foundation('close');
            },
            addPriceToEstimate(id) {
                this.$store.dispatch('ADD_SERVICES_ITEM_TO_ESTIMATE', id);
            }
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
